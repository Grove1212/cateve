<?php
require '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['username'];
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'follow') {
        $sql = "INSERT INTO 팔로잉 (본인, 팔로우하는사람) VALUES (:currentUserId, :userId)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['currentUserId' => $currentUserId, 'userId' => $userId]);
        echo '팔로잉';
    } elseif ($action === 'unfollow') {
        $sql = "DELETE FROM 팔로잉 WHERE 본인 = :currentUserId AND 팔로우하는사람 = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['currentUserId' => $currentUserId, 'userId' => $userId]);
        echo '팔로우';
    }
}
?>
