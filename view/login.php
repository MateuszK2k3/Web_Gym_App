<?php
include_once "../controllers/UserController.php";
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login Form</title>
  <!---Custom CSS File--->
  <link rel="stylesheet" href="welcome/css/loginStyle.css">
</head>
<body>
<div class="container">
  <div class="login form">
    <header>Logowanie</header>
      <form method="POST">
          <input type="text" name="login" placeholder="login" required="required">
          <input type="password" name="password" placeholder="hasło" required="required" >
          <input type="submit" class="button" name="submit" value="Zaloguj">
          <div class="err"><?php
              if (filter_input(INPUT_POST, 'submit')) {
                  $userController = new UserController();
                  $userController->login();
              }
              echo "</div>";
              if (filter_input(INPUT_GET, "akcja")=="wyswietlInfo") {
                  echo "<p style='color: green;'>Pomyślnie zarejestrowano.</p>";
              }
              ?>
          <a href="welcomePage.php"><input type="button" class="button_back" value="Wróć"></a>
      </form>
    <div class="signup">
        <span class="signup">Nie masz konta?
         <a href="signin.php">Zarejestruj sie</a>
        </span>
    </div>
  </div>
</div>
</body>
</html>
