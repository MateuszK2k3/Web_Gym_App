<?php include_once "../controllers/UserController.php";?>

<!DOCTYPE html>
<!-- Coding By CodingNepal - codingnepalweb.com -->
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Registration Form</title>
  <!---Custom CSS File--->
  <link rel="stylesheet" href="welcome/css/loginStyle.css">
</head>
<body>
<div class="container">
  <div class="registration form">
    <header>Rejestracja</header>
    <form method="POST">
      <input type="text" placeholder="login" required="required" name="login">
      <input type="password" placeholder="hasło" required="required" name="passwd1">
      <input type="password" placeholder="potwierdź hasło" required="required" name="passwd2">
      <input type="submit" class="button" value="Zarejestruj" name="signup">
      <div class="err"><?php
      if (filter_input(INPUT_POST, "signup")) {
          $userController = new UserController();
          try {
              $userController->register();
          } catch (Exception $e) {
              echo $e->getMessage();
          }
      }
          ?></div>
      <a href="welcomePage.php"><input type="button" class="button_back" value="Wróć"></a>
    </form>
    <div class="signup">
        <span class="signup">Masz już konto?
         <a href="login.php">Zaloguj</a>
        </span>
    </div>
  </div>
</div>
</body>
</html>
