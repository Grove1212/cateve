<?php
require '../db.php'; // 데이터베이스 연결

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 로그인된 사용자인지 확인
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 업로드</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="file"],
        button {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>게시글 업로드</h1>
        <form action="process_upload.php" method="POST" enctype="multipart/form-data">
            <label for="title">게시글 제목</label>
            <input type="text" id="title" name="title" placeholder="게시글 제목을 입력하세요" required>

            <label for="images">이미지 업로드</label>
            <input type="file" id="images" name="images[]" multiple required>

            <button type="submit">게시글 업로드</button>
        </form>
    </div>
</body>
</html>
