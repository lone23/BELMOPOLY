
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/style.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/login.css">
    <title>Login</title>
</head>
<body>
<div class="container">
    <div class="main">
        <div class="buttons">
            <div class="selector selected">LOGIN</div>
            <div class="selector" onclick="window.location.href='<?php echo URL; ?>autenticazione/RegistraUtenteView'">SIGN UP</div>
        </div>
        <div class="fields">
            <form method="POST" action="<?php echo URL; ?>autenticazione/verificaLogin">
                <input type="text" placeholder="E-MAIL" name="email" required>
                <input type="password" placeholder="PASSWORD" name="password" required>
                <input type="text" style="visibility: hidden;">
                <!-- Campo nascosto rimosso se non è necessario -->
                <input type="submit" value="LOGIN" class="button">
                <p><?php
                    if(Isset($_SESSION["ControlloLogin"])){
                        echo $_SESSION["ControlloLogin"];
                    }
                    ?></p>
            </form>

        </div>
    </div>

</div>
</body>
</html>

