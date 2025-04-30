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
        <img src="<?php echo URL?>application/views/images/arrow.png" onclick="window.location.href='<?php echo URL; ?>home/index'" alt="back" class="account">
        <div class="main">
            <div class="tools">
                <img src="<?php echo URL?>application/views/images/notification.png"  onclick="window.location.href='<?php echo URL; ?>GestioneAccount/mostraRichiesteAmicizia'" alt="notification" class="notification">
                <input
                        type="text"
                        class="search-bar"
                        placeholder="SEARCH..."
                        id="ricerca"
                >
                <img src="<?php echo URL ?>application/views/images/search.png" onclick="cercaUtente()" alt="search" class="search">
            </div>
            <div class="buttons">
                <div class="selector selected">FRIENDS</div>
                <div class="selector" onclick="window.location.href='<?php echo URL; ?>GestioneAccount/mostraUtenti'">USERS</div>
            </div>
            <div class="fields">
                <?php foreach ($amici as $utente) : ?>
                <div class="user">
                    <div class="name"> <?php echo $utente->getUsername(); ?></div>
                    <div class="request" onclick="rimuoviAmico('<?php echo $utente->getUsername(); ?>')">REMOVE</div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <script>

        function cercaUtente() {
            const valore = document.getElementById("ricerca").value;


            window.location.href = "<?php echo URL; ?>GestioneAccount/mostraAmicizie/" + encodeURIComponent(valore);

        }
        function rimuoviAmico(valore) {

            window.location.href = "<?php echo URL; ?>GestioneAccount/elliminaAmicizia/" + encodeURIComponent(valore);

        }
    </script>
</body>
</html>

