<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET 요청으로 post_id 확인
    $postId = $_GET['post_id'] ?? null;

    if ($postId) {
        try {
            // 트랜잭션 시작
            $pdo->beginTransaction();

            // 게시글 사진 삭제
            $sqlDeletePhotos = "DELETE FROM 게시글사진 WHERE 게시글ID = :postId";
            $stmtDeletePhotos = $pdo->prepare($sqlDeletePhotos);
            $stmtDeletePhotos->execute(['postId' => $postId]);

            // 게시글 삭제
            $sqlDeletePost = "DELETE FROM 게시글 WHERE 게시글ID = :postId";
            $stmtDeletePost = $pdo->prepare($sqlDeletePost);
            $stmtDeletePost->execute(['postId' => $postId]);

            // 트랜잭션 커밋
            $pdo->commit();

            // 성공 응답
            http_response_code(200);
            echo json_encode(['message' => '게시글이 성공적으로 삭제되었습니다.']);
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['message' => '삭제 중 오류가 발생했습니다.', 'error' => $e->getMessage()]);
        }
    } else {
        // 잘못된 요청 처리
        http_response_code(400);
        echo json_encode(['message' => '유효하지 않은 요청입니다. post_id가 필요합니다.']);
    }
} else {
    // GET이 아닌 요청 거부
    http_response_code(405);
    echo json_encode(['message' => '허용되지 않는 메서드입니다.']);
}
?>
