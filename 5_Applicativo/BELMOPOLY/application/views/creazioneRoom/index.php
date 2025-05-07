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

    /*
    gestione visualizzazione utente nella room
    */
    const username =  <?php echo json_encode($_SESSION['username']); ?>;
    const roomId = <?php echo json_encode($_SESSION['uuid']); ?>;

    const ws = new WebSocket("ws://localhost:3001");

    ws.onopen = () => {
        console.log("Connessione aperta");

        // Manda un oggetto con più dati
        ws.send(JSON.stringify({
            type: "join",
            username: username,
            roomId: roomId
        }));
    };

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);

        if (data.roomId === roomId) {
            const startButton = document.getElementById('start-button');
            console.log(data)
            const slots = document.querySelectorAll(".player-slot");
            slots.forEach((slot, index) => {
                slot.innerText = data.users[index] || "[EMPTY]";
            });
            if (data.users[0] == username){
                startButton.textContent = "START";
                startButton.classList.add('clickable');
                startButton.onclick = () => {
                    window.location.href = "<?php echo URL; ?>home/startGame";
                };
            }
        } else {
            console.log("⛔ Messaggio per un'altra stanza");
        }
    };

</script>


<div class="container">
    <img src="<?php echo URL?>application/views/images/arrow.png" onclick="window.location.href='<?php echo URL; ?>home/esciRoom'" alt="back" class="left-icon clickable">
    <div class="content">
        <div class="players">
            <div class="line">

                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p class="player-slot">[EMPTY]</p>
                </div>

                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p class="player-slot">[EMPTY]</p>
                </div>
            </div>
            <div class="line">

                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p class="player-slot">[EMPTY]</p>
                </div>

                <div class="player">
                    <img src="<?php echo URL?>application/views/images/account.png" alt="user" class="icon">
                    <p class="player-slot">[EMPTY]</p>
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
            <div id="start-button" class="button" >WAITING FOR HOST</div>
        </div>
    </div>
</div>
</body>
</html>