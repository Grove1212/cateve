<?php
require '../db.php';

// 게시글과 첫 번째 사진 조회
$sql = "
    SELECT
        게시글.게시글ID,
        게시글.글제목,
        게시글사진.사진URL
    FROM 게시글
    LEFT JOIN 게시글사진 ON 게시글.게시글ID = 게시글사진.게시글ID
    GROUP BY 게시글.게시글ID
    ORDER BY 게시글.작성일시 DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글</title>
    <link rel="stylesheet" href="../source/style.css">
</head>
<body>
    <div class="container">
      <!-- 헤더 바 -->
      <div class="header-bar">
          <!-- 검색창 -->
          <div class="search-bar">
              <input type="text" id="search" placeholder="검색...">
          </div>
          <!-- 업로드 버튼 -->
          <div class="upload-button-container">
              <a href="upload.php" class="upload-button">업로드</a>
          </div>
      </div>

      <!-- 게시글 그리드 -->
      <div class="post-grid">
          <?php foreach ($posts as $post): ?>
              <div class="post">
                  <a href="post_detail.php?post_id=<?php echo $post['게시글ID']; ?>">
                      <img src="../uploads/<?php echo htmlspecialchars($post['사진URL']); ?>" alt="Post Image">
                  </a>
              </div>
          <?php endforeach; ?>
      </div>

      <div class="bottom-bar">
        <a href="index.php">홈</a>
        <a href="../user/mypage.php">마이페이지</a>
      </div>
    </div>
</body>
</html>
