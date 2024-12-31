<?php
// 데이터베이스 연결
try {
    $pdo = new PDO("mysql:host=localhost;dbname=cateve;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "<script>
  alert('데이터베이스 연결에 실패했습니다: " . addslashes($e->getMessage()) . "');
      window.location.href = 'login.php';
      </script>";
    exit;
}

// 사용자 입력값 받기
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$catname = $_POST['catname'] ?? ''; // 변수명 수정
$catBirth = $_POST['catBirth'] ?? '';
$phonenumber = $_POST['phonenumber'] ?? '';

// 입력값 검증
if (empty($username) || empty($password) || empty($catname) || empty($catBirth) || empty($phonenumber)) {
    echo "<script>
        alert('모든 필드를 입력해야 합니다.');
        window.location.href = 'login.php';
    </script>";
    exit;
}

// 고양이 나이 계산
$birthDate = new DateTime($catBirth);
$today = new DateTime();
$age = $birthDate->diff($today)->y; // 생일과 오늘 날짜의 연도 차이 계산

// 비밀번호 해싱
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 데이터베이스에 사용자 정보 삽입
try {
    $sql = "INSERT INTO 회원 (회원ID, 비밀번호, 고양이이름, 고양이생일, 고양이나이, 전화번호)
            VALUES (:username, :password, :catname, :catBirth, :catAge, :phonenumber)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':catname', $catname);
    $stmt->bindParam(':catBirth', $catBirth);
    $stmt->bindParam(':catAge', $age);
    $stmt->bindParam(':phonenumber', $phonenumber);
    $stmt->execute();
    echo "<script>
        alert('회원 가입이 성공적으로 완료되었습니다!');
        window.location.href = 'login.php';
    </script>";
} catch (PDOException $e) {
    // 중복된 사용자 처리
    if ($e->getCode() == 23000) { // SQLSTATE 23000: 무결성 제약 조건 위반
        echo "<script>
            alert('이미 존재하는 사용자 이름입니다.');
            window.location.href = 'login.php';
        </script>";
    } else {
        echo "<script>
            alert('회원 가입 중 오류가 발생했습니다: " . addslashes($e->getMessage()) . "');
            window.location.href = 'login.php';
        </script>";
    }
}
?>
