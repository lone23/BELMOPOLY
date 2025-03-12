<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/register/index.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/register/login.css">
    <script src="../JavaScript/index.js"></script>
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="main">
            <div class="buttons">
                <div class="selector" onclick="window.location.href='<?php echo URL; ?>autenticazione/login'">LOGIN</div>
                <div class="selector selected">SIGN UP</div>
            </div>
             <div class="fields">
                <form method="POST" action="<?php echo URL; ?>autenticazione/RegistraUtente">
                    <input type="text" placeholder="USERNAME" name="username" required>
                    <br>
                    <input type="text" placeholder="EMAIL" name="email" required>
                    <br>
                    <input type="text" placeholder="PASSWORD" name="password" required>
                    <br>
                    <input type="submit" value="REGISTER" class="button">
                    <p><?php
                        if(Isset($_SESSION["ControlloRegister"])){
                            echo $_SESSION["ControlloRegister"];
                        }
                        ?></p>
                </form>

    </div>
</body>
</html>