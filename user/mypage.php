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

$current_user_id = $_SESSION['username']; // 세션에서 현재 로그인된 사용자 ID 가져오기

// 회원 정보 조회
$sql_user = "SELECT 회원ID, 고양이이름, 프로필사진, 프로필소개 FROM 회원 WHERE 회원ID = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $current_user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// 게시글 조회
$sql_posts = "
    SELECT 게시글.게시글ID, MIN(게시글사진.사진URL) AS 사진URL
    FROM 게시글
    LEFT JOIN 게시글사진 ON 게시글.게시글ID = 게시글사진.게시글ID
    WHERE 게시글.회원ID = :user_id
    GROUP BY 게시글.게시글ID
    ORDER BY 게시글.작성일시 DESC";
$stmt_posts = $pdo->prepare($sql_posts);
$stmt_posts->execute(['user_id' => $current_user_id]);
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

// 게시물 수, 팔로워 수, 팔로잉 수 조회
$sql_counts = "
    SELECT
        (SELECT COUNT(*) FROM 게시글 WHERE 회원ID = :user_id) AS post_count,
        (SELECT COUNT(*) FROM 팔로잉 WHERE 팔로우하는사람 = :user_id) AS follower_count,
        (SELECT COUNT(*) FROM 팔로잉 WHERE 본인 = :user_id) AS following_count";
$stmt_counts = $pdo->prepare($sql_counts);
$stmt_counts->execute(['user_id' => $current_user_id]);
$counts = $stmt_counts->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이페이지</title>
    <link rel="stylesheet" href="../source/mypage-style.css">
</head>
<body>
    <div class="mypage-container">
        <!-- 프로필 섹션 -->
        <div class="profile-header">
            <div class="profile-picture">
                <img src="../source/<?php echo htmlspecialchars($user['프로필사진']); ?>" alt="프로필 사진">
            </div>
            <div class="profile-stats">
                <div class="stat-item">
                    <strong><?php echo $counts['post_count']; ?></strong> 게시물
                </div>
                <a href="../user/follower.php">
                  <div class="stat-item">
                      <strong><?php echo $counts['follower_count']; ?></strong> 팔로워
                  </div>
                </a>
                <a href="../user/following.php">
                  <div class="stat-item">
                    <strong><?php echo $counts['following_count']; ?></strong> 팔로잉
                  </div>
                </a>
            </div>
        </div>

        <!-- 프로필 상세 정보 -->
        <div class="profile-details">
            <h2><?php echo htmlspecialchars($user['회원ID']); ?></h2>
            <p><strong>고양이 이름:</strong> <?php echo htmlspecialchars($user['고양이이름']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($user['프로필소개'])); ?></p>
        </div>

        <!-- 게시글 그리드 -->
        <div class="post-grid">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <a href="../post/post_detail.php?post_id=<?php echo $post['게시글ID']; ?>">
                        <img src="../uploads/<?php echo htmlspecialchars($post['사진URL']); ?>" alt="게시글 이미지">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
