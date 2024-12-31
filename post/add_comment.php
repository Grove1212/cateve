<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['username']; // 로그인된 사용자 ID

    $sql = "INSERT INTO 댓글 (게시글ID, 작성자, 댓글내용) VALUES (:postId, :userId, :content)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['postId' => $postId, 'userId' => $userId, 'content' => $comment]);

    header("Location: post_detail.php?post_id=$postId");
    exit;
}
?>
