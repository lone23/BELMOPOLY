
<html>
<head>
    <title>Home</title>
</head>

<form method="POST" action="<?php echo URL?>home/verificaLogin">
    Email:    <input type="text" name="email" required><br>
    Password: <input type="text" name="password" required><br>

    <p><?php
        if(Isset($_SESSION["ControlloLogin"])){
            echo $_SESSION["ControlloLogin"];
        }
        ?></p>

    <input type="submit" value="Seleziona">
</form>

