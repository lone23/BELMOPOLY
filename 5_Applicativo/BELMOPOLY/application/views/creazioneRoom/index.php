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
<script>
    const socket = new WebSocket('ws://localhost:3000');

    // Una volta che la connessione è stabilita, unisciti alla stanza
    socket.onopen = () => {
        const room = "<?php echo $_SESSION["uuid"]?>";  // Inserisci l'UUID della stanza
        socket.send(JSON.stringify({
            joinRoom: room
        }));
    };

    // Quando il server invia un messaggio, gestiscilo
    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);

        if (data.startGame) {
            // Logica per iniziare il gioco
            console.log("Il gioco sta per iniziare...");
            window.location.href = '<?php echo URL ?>board/index';
        }
    };

    // Gestione errori e disconnessione
    socket.onerror = (error) => {
        console.error("Errore WebSocket:", error);
    };

    socket.onclose = () => {
        console.log("Connessione WebSocket chiusa");
    };

</script>

<div class="container">
    <img src="<?php echo URL?>application/views/images/arrow.png" onclick="window.location.href='<?php echo URL; ?>home/esciRoom'" alt="back" class="left-icon clickable">
    <div class="content">
        <div class="players">
            <div class="line">
                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p>YOU</p>
                </div>
                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p>[EMPTY]</p>
                </div>
            </div>
            <div class="line">
                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p>[EMPTY]</p>
                </div>
                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p>[EMPTY]</p>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="list">
                <?php foreach ($amici as $utente) : ?>
                    <div class="user">
                        <div class="name"><?php echo $utente->getUsername(); ?></div>
                        <div class="invite clickable" onclick="window.location.href='<?php echo URL; ?>home/invitaRoom/<?php echo $utente->getUsername(); ?>'">INVITE</div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="button clickable" onclick="window.location.href='<?php echo URL; ?>home/startGame'">START</div>
        </div>
    </div>
</div>
</body>
</html>