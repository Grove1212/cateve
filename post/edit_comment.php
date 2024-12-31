<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 필수 데이터 검증
    $commentId = $_POST['comment_id'] ?? null;
    $newContent = $_POST['new_content'] ?? null;
    $postId = $_POST['post_id'] ?? null; // 게시글 ID
    $userId = $_SESSION['username'] ?? null;

    if (!$commentId) {
        die("잘못된 요청입니다. commentId가 누락되었습니다.");
    }
    if (!$newContent) {
        die("잘못된 요청입니다. newContent가 누락되었습니다.");
    }
    if (!$postId) {
        die("잘못된 요청입니다. postId가 누락되었습니다.");
    }
    if (!$userId) {
        die("잘못된 요청입니다. userId가 누락되었습니다.");
    }

    // 댓글 작성자 확인
    $sql = "SELECT 작성자 FROM 댓글 WHERE 댓글ID = :commentId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['commentId' => $commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        die("존재하지 않는 댓글입니다.");
    }

    if ($comment['작성자'] !== $userId) {
        die("수정 권한이 없습니다.");
    }

    // 댓글 내용 업데이트
    $sql = "UPDATE 댓글 SET 댓글내용 = :newContent WHERE 댓글ID = :commentId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'newContent' => $newContent,
        'commentId' => $commentId
    ]);

    // 수정 후 게시글 상세 페이지로 리디렉션
    header("Location: post_detail.php?post_id=$postId");
    exit;
} else {
    die("잘못된 접근입니다.");
}
?>
