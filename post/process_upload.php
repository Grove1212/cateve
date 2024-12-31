<?php
require '../db.php'; // 데이터베이스 연결

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function uploadFiles($files, $postId, $pdo) {
    $targetDir = "../uploads/";
    foreach ($files['tmp_name'] as $key => $tmpName) {
        $fileName = basename($files['name'][$key]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 유효성 검사 (파일 형식 확인)
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception("허용되지 않는 파일 형식입니다: $fileName");
        }

        // 파일 이동
        if (move_uploaded_file($tmpName, $targetFile)) {
            // 게시글사진 테이블에 데이터 삽입
            $sql = "INSERT INTO 게시글사진 (게시글ID, 사진URL) VALUES (:postId, :photoURL)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['postId' => $postId, 'photoURL' => $fileName]);
        } else {
            throw new Exception("파일 업로드 중 오류가 발생했습니다: $fileName");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $userId = $_SESSION['username']; // 세션에서 사용자 ID 가져오기
    $likes = 0; // 초기 좋아요 개수

    try {
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 게시글 테이블에 데이터 삽입
        $sql = "INSERT INTO 게시글 (글제목, 좋아요개수, 회원ID)
                VALUES (:title, :likes, :userId)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['title' => $title, 'likes' => $likes, 'userId' => $userId]);

        // 생성된 게시글ID 가져오기
        $postId = $pdo->lastInsertId();

        // 파일 업로드 처리
        uploadFiles($_FILES['images'], $postId, $pdo);

        // 트랜잭션 커밋
        $pdo->commit();

        // 업로드 성공 시 리다이렉트
        echo "게시글이 업로드되었습니다.";
        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        // 트랜잭션 롤백
        $pdo->rollBack();
        die("업로드 중 오류 발생: " . $e->getMessage());
    }
}
?>
