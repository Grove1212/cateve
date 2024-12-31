<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $commentId = $_POST['comment_id'];
  $postId = $_POST['post_id'];
  $userId = $_SESSION['username']; // 로그인된 사용자 ID

    // 댓글 작성자 확인
    $sql = "SELECT 작성자 FROM 댓글 WHERE 댓글ID = :commentId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['commentId' => $commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment && $comment['작성자'] === $userId) {
        // 삭제 처리
        $sql = "DELETE FROM 댓글 WHERE 댓글ID = :commentId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['commentId' => $commentId]);
    }

    header("Location: post_detail.php?post_id=$postId");
    exit;
}
?>
