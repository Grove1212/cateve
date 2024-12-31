<?php
require '../db.php';

// 특정 게시글 정보 조회
$postId = $_GET['post_id'];

$sql = "
    SELECT
        게시글.글제목,
        게시글.좋아요개수,
        게시글.회원ID,
        게시글.작성일시,
        회원.프로필사진 AS 프로필사진
    FROM 게시글
    LEFT JOIN 회원 ON 게시글.회원ID = 회원.회원ID
    WHERE 게시글.게시글ID = :postId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['postId' => $postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// 게시글의 모든 사진 조회
$sqlPhotos = "SELECT 사진URL FROM 게시글사진 WHERE 게시글ID = :postId";
$stmtPhotos = $pdo->prepare($sqlPhotos);
$stmtPhotos->execute(['postId' => $postId]);
$photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

// 댓글 정보 조회
$sqlComments = "SELECT * FROM 댓글 WHERE 게시글ID = :postId ORDER BY 댓글일시 DESC";
$stmtComments = $pdo->prepare($sqlComments);
$stmtComments->execute(['postId' => $postId]);
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);


function isFollowing($userId) {
    global $pdo;
    $currentUserId = $_SESSION['username']; // 로그인된 사용자 ID
    $sql = "SELECT COUNT(*) FROM 팔로잉 WHERE 본인 = :currentUserId AND 팔로우하는사람 = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['currentUserId' => $currentUserId, 'userId' => $userId]);
    return $stmt->fetchColumn() > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['글제목']); ?></title>
    <link rel="stylesheet" href="../source/post.css">
</head>
<body>
    <div class="container">
        <!-- 프로필 및 회원 정보 -->
        <div class="post-header">
            <img src="../source/<?php echo htmlspecialchars($post['프로필사진']); ?>" alt="Profile Picture" class="profile-pic">
            <span class="username"><?php echo htmlspecialchars($post['회원ID']); ?></span>
            <!-- 팔로우/팔로잉 버튼 -->
            <button id="follow-btn"
                data-following="<?php echo isFollowing($post['회원ID']) ? '1' : '0'; ?>"
                data-user-id="<?php echo htmlspecialchars($post['회원ID']); ?>">
                <?php echo isFollowing($post['회원ID']) ? '팔로잉' : '팔로우'; ?>
            </button>
            <span class="post-date"><?php echo htmlspecialchars($post['작성일시']); ?></span>

        </div>

        <!-- 수정 및 삭제 버튼 -->
        <?php if ($_SESSION['username'] === $post['회원ID']): ?>
            <div class="post-actions">
                <button onclick="editPost()">수정</button>
                <button onclick="confirmDelete()">삭제</button>
            </div>
        <?php endif; ?>

        <!-- 슬라이더 (여러 사진) -->
        <div class="slider">
            <?php foreach ($photos as $index => $photo): ?>
                <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="../uploads/<?php echo htmlspecialchars($photo['사진URL']); ?>" alt="Post Image">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 좋아요 -->
        <div class="post-likes">
            <button id="like-btn" data-liked="<?php echo isset($_SESSION['liked_posts'][$postId]) ? '1' : '0'; ?>">
                <?php echo isset($_SESSION['liked_posts'][$postId]) ? '좋아요 취소' : '좋아요'; ?>
            </button>
            <p id="like-count">좋아요: <?php echo htmlspecialchars($post['좋아요개수']); ?>개</p>
        </div>

        <!-- 설명 -->
        <div class="post-description">
            <p><?php echo htmlspecialchars($post['글제목']); ?></p>
        </div>

        <!-- 댓글 영역 -->
        <div class="comments-section">
            <h3>댓글</h3>

            <!-- 댓글 작성 폼 -->
            <form method="POST" action="add_comment.php">
              <input type="hidden" name="post_id" value="<?php echo $postId; ?>"> <!-- post_id 전달 -->
              <textarea name="comment" placeholder="댓글을 입력하세요..." required></textarea>
              <button type="submit">작성</button>
            </form>

            <!-- 댓글 목록 -->
            <div id="comments">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment" data-comment-id="<?php echo $comment['댓글ID']; ?>">
                        <span class="comment-user"><?php echo htmlspecialchars($comment['작성자']); ?></span>
                        <span class="comment-date"><?php echo htmlspecialchars($comment['댓글일시']); ?></span>

                        <!-- 댓글 내용 -->
                        <div class="comment-content">
                            <p><?php echo htmlspecialchars($comment['댓글내용']); ?></p>
                        </div>

                        <!-- 수정 및 삭제 버튼 -->
                        <?php if ($_SESSION['username'] === $comment['작성자']): ?>
                            <button class="edit-btn" onclick="showEditForm(<?php echo $comment['댓글ID']; ?>)">수정</button>
                            <form method="POST" action="delete_comment.php" style="display:inline;">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['댓글ID']; ?>">
                                <input type="hidden" name="post_id" value="<?php echo $postId; ?>"> <!-- post_id 전달 -->
                                <button type="submit" onclick="return confirm('댓글을 삭제하시겠습니까?');">삭제</button>
                            </form>

                            <!-- 댓글 수정 폼 (숨김 상태로 시작) -->
                            <form method="POST" action="edit_comment.php" class="edit-form" id="edit-form-<?php echo $comment['댓글ID']; ?>" style="display:none;">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['댓글ID']; ?>">
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($postId); ?>"> <!-- 게시글 ID 전달 -->
                                <textarea name="new_content" required><?php echo htmlspecialchars($comment['댓글내용']); ?></textarea>
                                <button type="submit">저장</button>
                                <button type="button" onclick="hideEditForm(<?php echo $comment['댓글ID']; ?>)">취소</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


    </div>

    <!-- JavaScript 슬라이더 및 버튼 기능 -->
    <script>
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        // 초기 슬라이드 표시
        showSlide(currentSlide);

        // 키보드로 슬라이드 이동 (왼쪽/오른쪽 화살표)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') nextSlide();
            if (e.key === 'ArrowLeft') prevSlide();
        });

        // 수정 버튼 클릭 시 동작
        function editPost() {
            const postId = <?php echo json_encode($postId); ?>;
            window.location.href = `edit_post.php?post_id=${postId}`;
        }

        // 삭제 확인 팝업 및 동작
        function confirmDelete() {
            const postId = <?php echo json_encode($postId); ?>;
            if (confirm("정말 삭제하시겠습니까?")) {
                // 서버로 삭제 요청
                fetch(`delete_post.php?post_id=${postId}`, {
                    method: 'GET'
                })
                .then(response => {
                    if (response.ok) {
                        alert("삭제되었습니다.");
                        window.location.href = 'index.php'; // 게시글 목록 페이지로 이동
                    } else {
                        alert("삭제에 실패했습니다.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("에러가 발생했습니다.");
                });
            }
        }

        document.getElementById('follow-btn').addEventListener('click', function() {
            const button = this;
            const userId = button.getAttribute('data-user-id');
            const isFollowing = button.getAttribute('data-following') === '1';
            const action = isFollowing ? 'unfollow' : 'follow';

            fetch('../user/follow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ user_id: userId, action: action })
            })
            .then(response => response.text())
            .then(text => {
                button.textContent = text;
                button.setAttribute('data-following', isFollowing ? '0' : '1');
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // 좋아요 토글
        document.getElementById('like-btn').addEventListener('click', function () {
            const likeBtn = this;
            const isLiked = likeBtn.getAttribute('data-liked') === '1';
            const postId = <?php echo json_encode($postId); ?>;
            const action = isLiked ? 'unlike' : 'like';

            fetch(`like_post.php?post_id=${postId}&action=${action}`, { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likeBtn.setAttribute('data-liked', isLiked ? '0' : '1');
                        likeBtn.textContent = isLiked ? '좋아요' : '좋아요 취소';
                        document.getElementById('like-count').textContent = `좋아요: ${data.likeCount}개`;
                    } else {
                        alert("오류가 발생했습니다.");
                    }
                });
        });

        // 댓글 작성
        document.getElementById('comment-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const commentText = this.comment.value;
            const postId = <?php echo json_encode($postId); ?>;

            fetch('add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ postId: postId, comment: commentText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // 새로고침
                } else {
                    alert("댓글 작성에 실패했습니다.");
                }
            });
        });

        // 댓글 수정/삭제 이벤트 리스너 추가
        document.querySelectorAll('.edit-comment').forEach(button => {
            button.addEventListener('click', function () {
                const commentId = this.closest('.comment').getAttribute('data-comment-id');
                const newContent = prompt("댓글 내용을 수정하세요:");
                if (newContent) {
                    fetch('edit_comment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ commentId: commentId, newContent: newContent })
                    }).then(() => location.reload());
                }
            });
        });

        document.querySelectorAll('.delete-comment').forEach(button => {
            button.addEventListener('click', function () {
                const commentId = this.closest('.comment').getAttribute('data-comment-id');
                if (confirm("댓글을 삭제하시겠습니까?")) {
                    fetch(`delete_comment.php?comment_id=${commentId}`, { method: 'GET' })
                        .then(() => location.reload());
                }
            });
        });
    </script>
    <script>
        // 수정 폼 표시
        function showEditForm(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            editForm.style.display = 'block';

            const editButton = document.querySelector(`.comment[data-comment-id="${commentId}"] .edit-btn`);
            editButton.style.display = 'none'; // 수정 버튼 숨기기
        }

        // 수정 폼 숨기기
        function hideEditForm(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            editForm.style.display = 'none';

            const editButton = document.querySelector(`.comment[data-comment-id="${commentId}"] .edit-btn`);
            editButton.style.display = 'inline-block'; // 수정 버튼 다시 표시
        }
    </script>


</body>
</html>
