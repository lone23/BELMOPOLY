<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/style.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/room.css">
    <title>Create Room</title>
</head>
<body>
<div class="container">
    <img src="<?php echo URL?>application/views/images/arrow.png" onclick="window.location.href='<?php echo URL; ?>home/esciRoom'" alt="account" class="account">
    <div class="content">
        <div class="players">
            <div class="line">
                <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
            </div>
            <div class="line">
                <img src="<?php echo URL?>application/views/images/account.png" alt="account" class="icon">
                <img src="<?php echo URL?>application/views/images/account.png" alt="account" class="icon">
            </div>
        </div>
        <div class="right">
            <div class="list">
                <?php foreach ($amici as $utente) : ?>
                    <div class="user"> <?php echo $utente->getUsername(); ?> <div class="invite">INVITE</div></div>
                <?php endforeach; ?>
            </div>
            <div class="button" onclick="window.location.href='<?php echo URL; ?>Board/index'">START</div>
        </div>
    </div>
</div>
</body>
</html>