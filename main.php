<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect Example</title>
    <style>
      /* Flexbox로 중앙 정렬 */
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
    <?php
      // 1초 후 로그인 페이지로 리디렉션
      header("Refresh: 2; URL=login_signup/login.php");
    ?>
  </head>
  <body>
    <h1>Cat Everywhere</h1>
    <img src="source/cat1.jpg" alt="Cat Image">
  </body>
</html>
