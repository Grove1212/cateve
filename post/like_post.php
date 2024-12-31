<?php
require '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$postId = $_GET['post_id'];
$action = $_GET['action'];
$userId = $_SESSION['username'];

if (!$userId) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
    exit;
}

if ($action === 'like') {
    $sql = "INSERT INTO 좋아요 (회원ID, 게시글ID) VALUES (:userId, :postId)";
} elseif ($action === 'unlike') {
    $sql = "DELETE FROM 좋아요 WHERE 회원ID = :userId AND 게시글ID = :postId";
} else {
    echo json_encode(['success' => false, 'message' => '유효하지 않은 요청입니다.']);
    exit;
}

$stmt = $pdo->prepare($sql);
$success = $stmt->execute(['userId' => $userId, 'postId' => $postId]);

// 좋아요 개수 가져오기
$sqlLikes = "SELECT COUNT(*) AS 좋아요개수 FROM 좋아요 WHERE 게시글ID = :postId";
$stmtLikes = $pdo->prepare($sqlLikes);
$stmtLikes->execute(['postId' => $postId]);
$likeCount = $stmtLikes->fetchColumn();

echo json_encode(['success' => $success, 'likeCount' => $likeCount]);
?>
