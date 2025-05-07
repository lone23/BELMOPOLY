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
        <img src="<?php echo URL?>application/views/images/account.png" onclick="window.location.href='<?php echo URL; ?>autenticazione/logout'" alt="account" class="left-icon clickable">
        <img src="<?php echo URL?>application/views/images/friends.png" onclick="window.location.href='<?php echo URL; ?>GestioneAccount/mostraAmicizie'" alt="friends" class="right-icon clickable">
        <div class="belmopoly">BELMOPOLY</div>
        <div onclick="window.location.href='<?php echo URL; ?>Home/creaRoom'" class="button clickable">NEW GAME</div>
        <div class="button clickable">CONTINUE</div>
        <a href="<?php echo URL; ?>application/views/imports/rules.pdf" download>
        <div class="button clickable">DOWNLOAD RULES</div>
        </a>
    </div>
</body>
</html>
