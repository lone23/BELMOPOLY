<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/style.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/friends.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <img src="<?php echo URL ?>application/views/images/arrow.png" onclick="window.location.href='<?php echo URL; ?>home/index'" alt="back" class="left-icon clickable">
        <div class="main">
            <div class="tools">
                <img src="<?php echo URL ?>application/views/images/friends.png" onclick="window.location.href='<?php echo URL; ?>GestioneAccount/mostraAmicizie'" alt="friends" class="toggle-option clickable">
                <input type="text" class="search-bar" placeholder="SEARCH...">
                <img src="<?php echo URL ?>application/views/images/search.png" alt="search" class="search clickable">
            </div>
            <div class="buttons">
                <div class="selector selected">REQUESTS</div>
                <div class="selector" onclick="window.location.href='<?php echo URL; ?>home/mostraRichiesteRoom'">INVITES</div>
            </div>
            <div class="fields">
                <?php foreach ($amici as $utente) : ?>
                <div class="user">
                    <div class="name"><?php echo $utente->getUsername(); ?></div>
                    <div class="options">
                        <div class="option clickable" onclick="rifiutaInvito('<?php echo $utente->getUsername(); ?>')">DECLINE</div>
                        <div class="option clickable" onclick="accettaInvito('<?php echo $utente->getUsername(); ?>')">ACCEPT</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <script>
        function rifiutaInvito(valore) {
            window.location.href = "<?php echo URL; ?>GestioneAccount/rifiutaRichiestaAmicizia/" + encodeURIComponent(valore);
        }
        function accettaInvito(valore) {
            window.location.href = "<?php echo URL; ?>GestioneAccount/accettaRichiestaAmicizia/" + encodeURIComponent(valore);
        }
    </script>
</body>
</html>
