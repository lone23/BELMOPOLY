<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/index.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/friends.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <img src="<?php echo URL ?>application/views/images/arrow.png"  onclick="window.location.href='<?php echo URL; ?>home/index'" alt="back" class="account">
        <div class="main">
            <div class="tools">
                <img src="<?php echo URL ?>application/views/images/friends.png"  onclick="window.location.href='<?php echo URL; ?>GestioneUtenti/MostraRichiesteAmicizia'" class="notification">
                <input type="text" class="search-bar" placeholder="SEARCH...">
                <img src="<?php echo URL ?>application/views/images/search.png" alt="search" class="search">
            </div>
            <div class="buttons">
                <div class="selector"  onclick="window.location.href='<?php echo URL; ?>GestioneAccount/mostraRichiesteAmicizia'">REQUESTS</div>
                <div class="selector selected">INVITES</div>
            </div>
            <div class="fields">
                <?php foreach ($users as $utente) : ?>
                    <div class="user">
                        <div class="name"><?php echo $utente; ?></div>
                        <div class="options">
                            <div class="option" onclick="rifiutaInvito('<?php echo $utente; ?>')">DECLINE</div>
                            <div class="option" onclick="window.location.href = '<?php echo URL; ?>home/accettaRichiesteRoom'">ACCEPT</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

<script>
    function rifiutaInvito(valore) {
         window.location.href = "<?php echo URL; ?>home/elliminaInvitoRoom/" + encodeURIComponent(valore);
    }
</script>
</body>
</html>
