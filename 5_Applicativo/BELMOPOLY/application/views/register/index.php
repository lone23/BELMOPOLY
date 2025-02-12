
<html>
<head>
    <title>Register</title>
</head>

<form method="POST" action="<?php echo URL?>autenticazione/RegistraUtente">
    Email:    <input type="text" name="email" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="text" name="password" required><br>

    <input type="submit" value="Seleziona">

    <p><?php
        if(Isset($_SESSION["ControlloRegister"])){
            echo $_SESSION["ControlloRegister"];
        }
        ?></p>
</form>
<a href="<?php echo URL; ?>autenticazione/login">login</a>
