<?php
// 데이터베이스 연결 설정
try {
    $pdo = new PDO("mysql:host=localhost;dbname=cateve;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 세션 시작
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // 로그인이 안 된 경우 로그인 페이지로 이동
    exit;
}
?>
