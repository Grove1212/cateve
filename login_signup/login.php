<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <style>
      /* 전체 페이지 배경 이미지 설정 */
      body {
        background: url('../source/cat1.jpg') no-repeat center center fixed; /* 배경 이미지 설정 */
        background-size: cover; /* 화면 전체에 이미지가 꽉 차도록 설정 */
        position: relative; /* 자식 요소를 위한 기준점 설정 */
        margin: 0;
        padding: 0;
      }

      /* 배경 투명도 효과 */
      body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7); /* 투명도를 70%로 설정해 이미지가 더 잘 보이도록 조정 */
        z-index: 1; /* 내용 위에 배치 */
      }

      /* 로그인 및 회원가입 폼 컨테이너 */
      .container {
        position: relative;
        z-index: 2; /* 투명 배경 위에 폼을 표시 */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
      }

      form {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 90%; /* 폼 너비를 전체의 90%로 설정해 여백 추가 */
        max-width: 400px; /* 최대 너비를 설정 */
        margin: 20px 0;
        box-sizing: border-box; /* 패딩 포함 크기 설정 */
      }

      h1 {
        margin-bottom: 20px;
        font-size: 24px;
        text-align: center;
      }

      input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box; /* 패딩 포함 크기 설정 */
      }

      button {
        width: 100%;
        padding: 10px;
        background-color: #3E3940;
        color: white;
        font-size: 16px;
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
      <form action="process_login.php" method="POST">
        <h1>Login</h1>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>

      <form action="process_signup.php" method="POST">
        <h1>Sign Up</h1>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="catname" placeholder="고양이이름" required>
        <input type="date" name="catBirth" required>
        <input type="text" name="phonenumber" placeholder="전화번호(예: 000-0000-0000)" required>
        <button type="submit">Sign Up</button>
      </form>
    </div>
  </body>
</html>
