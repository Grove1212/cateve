<?php
require '../db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 현재 로그인된 사용자 ID 가져오기
$currentUserId = $_SESSION['username'];

// 팔로잉하고 있는 회원 목록 가져오기
$sql = "
    SELECT
        회원ID,
        프로필사진
    FROM 회원
    WHERE 회원ID IN (SELECT 본인 FROM 팔로잉 WHERE 팔로우하는사람 = :currentUserId)";

$stmt = $pdo->prepare($sql);
$stmt->execute(['currentUserId' => $currentUserId]);
$followingList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>팔로워 목록</title>
    <link rel="stylesheet" href="../source/follow_list.css">
</head>
<body>
    <div class="container">
        <h1>팔로워</h1>
        <?php if (count($followingList) > 0): ?>
            <?php foreach ($followingList as $user): ?>
                <div class="following-item">
                    <img src="../source/<?php echo htmlspecialchars($user['프로필사진']); ?>" alt="Profile Picture" class="profile-pic">
                    <span class="username"><?php echo htmlspecialchars($user['회원ID']); ?></span>
                    <button class="follow-btn"
                        data-following="1"
                        data-user-id="<?php echo htmlspecialchars($user['회원ID']); ?>">
                        팔로워
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
          <div class="following-item">
            <p>회원님을 팔로워 중인 회원이 없습니다.</p>
          </div>
        <?php endif; ?>
      </div>

    <script>
        // 팔로우/언팔로우 버튼 동작
        document.querySelectorAll('.follow-btn').forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-user-id');
                const isFollowing = this.getAttribute('data-following') === '1';
                const action = isFollowing ? 'unfollow' : 'follow';

                fetch('../user/follow.php', { // follow.php 경로 확인
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ user_id: userId, action: action })
                })
                .then(response => response.text())
                .then(text => {
                    this.textContent = text;
                    this.setAttribute('data-following', isFollowing ? '0' : '1');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>
