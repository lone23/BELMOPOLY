
<html>
<head>
    <title>Home</title>
</head>
<body

<form method="POST" action="<?php echo URL?>home/verificaLogin">
    Email:    <input type="text" name="email" required><br>
    Password: <input type="text" name="password" required><br>

    <input type="submit" value="Seleziona">
    <p><?php
        if(Isset($_SESSION["ControlloLogin"])){
            echo $_SESSION["ControlloLogin"];
        }
        ?></p>
</form>
