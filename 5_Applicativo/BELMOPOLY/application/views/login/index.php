
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/login/style.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/login/styleLogin.css">
    <title>Login</title>
</head>
<body>
<div class="container">
    <div class="main">
        <div class="buttons">
            <div class="selector selected">LOGIN</div>
            <div class="selector" onclick="<?php echo URL; ?>autenticazione/RegistraUtenteView">SIGN UP</div>
        </div>
        <div class="fields">
                <input type="text" placeholder="E-MAIL">
                <br>
                <input type="text" placeholder="PASSWORD">
                <br>
                <input type="text" style="visibility: hidden;">
                <br>
                <div type="submit" value="Seleziona" class="button">LOGIN</div>
        </div>
    </div>

</div>
</body>
</html>