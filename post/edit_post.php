<?php
require '../db.php';

// 게시글 ID 확인
$postId = $_GET['post_id'] ?? null;

if (!$postId) {
    die('게시글 ID가 필요합니다.');
}

// 기존 게시글 정보 조회
$sql = "
    SELECT
        글제목,
        좋아요개수
    FROM 게시글
    WHERE 게시글ID = :postId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['postId' => $postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die('게시글을 찾을 수 없습니다.');
}

// POST 요청으로 수정된 데이터 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedTitle = $_POST['title'] ?? '';

    // 필수 입력 검증
    if (trim($updatedTitle) === '') {
        $error = '모든 필드를 채워주세요.';
    } else {
        // 게시글 업데이트
        $sqlUpdate = "
            UPDATE 게시글
            SET 글제목 = :title
            WHERE 게시글ID = :postId";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            'title' => $updatedTitle,
            'postId' => $postId
        ]);

        // 수정 완료 후 리다이렉트
        header("Location: post_detail.php?post_id=$postId");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정</title>
    <link rel="stylesheet" href="../source/edit_post.css">
</head>
<body>
    <div class="container">
        <h1>게시글 수정</h1>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="title">내용</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['글제목']); ?>" required>
            </div>

            <button type="submit">수정 완료</button>
            <button type="button" onclick="cancelEdit()">취소</button>
        </form>
    </div>

    <script>
        function cancelEdit() {
            const postId = <?php echo json_encode($postId); ?>;
            window.location.href = `view_post.php?post_id=${postId}`;
        }
    </script>
</body>
</html>
