<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/style.css">
    <title>Main Page</title>
</head>
<body>
    <div class="container">
        <img src="<?php echo URL?>application/views/images/account.png" onclick="window.location.href='<?php echo URL; ?>autenticazione/logout'" alt="account" class="account">
        <img src="<?php echo URL?>application/views/images/friends.png" onclick="window.location.href='<?php echo URL; ?>'" alt="friends" class="friends">
        <div class="belmopoly">BELMOPOLY</div>
        <div onclick="window.location.href='<?php echo URL; ?>Home/creaRoom'" class="button">NEW GAME</div>
        <br>
        <div class="button">CONTINUE</div>
        <br>
        <div class="button">CHARACTER</div>
    </div>
</body>
</html>
