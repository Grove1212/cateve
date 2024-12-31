<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
      body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
      }

      h1 {
        margin: 20px 0;
        text-align: center;
      }

      img {
        max-width: 80%;
        height: auto;
      }
    </style>
  </head>
  <body>
    <?php
    session_start(); // 세션 시작

    // 데이터베이스 연결 정보
    $host = 'localhost'; // 데이터베이스 호스트
    $dbname = 'cateve'; // 데이터베이스 이름
    $username = 'root'; // 데이터베이스 사용자 이름
    $password = ''; // 데이터베이스 비밀번호

    try {
        // 데이터베이스 연결 생성
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 폼에서 입력받은 값 가져오기
        $input_username = $_POST['username'];
        $input_password = $_POST['password'];

        // 회원 정보 확인 쿼리
        $sql = "SELECT * FROM 회원 WHERE 회원ID = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $input_username, PDO::PARAM_STR);
        $stmt->execute();

        // 결과 가져오기
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 비밀번호 확인 (예: password_hash를 사용한 경우)
            if (password_verify($input_password, $user['비밀번호'])) {
                // 세션 시작
                session_start();

                // 로그인 성공: 세션에 사용자 정보 저장
                $_SESSION['username'] = $user['회원ID']; // 회원ID를 세션에 저장

                // 로그인 성공 메시지 및 페이지 리디렉션
                echo "<script>
                    alert('로그인 성공!');
                    window.location.href = '../post/index.php'; // 게시글 목록 페이지로 이동
                </script>";
            } else {
                // 비밀번호 불일치
                echo "<script>
                    alert('비밀번호가 일치하지 않습니다.');
                    window.location.href = 'login.php';
                </script>";
            }
        } else {
            // 사용자 정보 없음
            echo "<script>
                alert('사용자 이름이 올바르지 않습니다.');
                window.location.href = 'login.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
    ?>
  </body>
</html>
