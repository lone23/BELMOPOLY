<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/home/index.css">
    <title>Main Page</title>
</head>
<body>
    <div class="container">
        <img src="<?php echo URL?>application/views/home/account.png" onclick="goToPage('Login')" alt="account" class="account">
        <img src="<?php echo URL?>application/views/home/friends.png" onclick="window.location.href='<?php echo URL; ?>GestioneAccount/mostraRichiesteAmicizia'" alt="friends" class="friends">
        <div class="belmopoly">BELMOPOLY</div>
        <div onclick="window.location.href='<?php echo URL; ?>board/index'"" class="button">NEW GAME</div>
        <br>
        <div class="button">CONTINUE</div>
        <br>
        <div class="button">CHARACTER</div>
    </div>
</body>
</html>
