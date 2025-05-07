const WebSocket = require('ws');
const rooms = {};

const wss = new WebSocket.Server({ port: 3001 }, () => {
    console.log("WebSocket server listening on ws://localhost:3001");
});

wss.on('connection', function connection(ws) {
    console.log("Client connected");

    let username;
    let roomId;

    ws.on('message', function incoming(message) {
        const msg = JSON.parse(message.toString());

        console.log("Ricevuto:", msg);

        // Prendere i singoli dati
        username = msg.username;
        roomId = msg.roomId;

        // Inizializza la stanza se non esiste
        if (!rooms[roomId]) rooms[roomId] = [];

        // Evita duplicati (es. refresh)
        if (!rooms[roomId].includes(username)) {
            rooms[roomId].push(username);
        }

        // Messaggio contenente tutti i dati
        const userlist = {
            type: "userlist",
            users: rooms[roomId],
            roomId: roomId
        };

        // Manda il messaggio a tutti i client connessi
        wss.clients.forEach(function each(client) {
            if (client.readyState === WebSocket.OPEN) {
                client.send(JSON.stringify(userlist));
            }
        });
    });

    // Gestisci la chiusura della connessione
    ws.on('close', () => {
        if (username && roomId) {
            // Rimuovi l'utente dalla lista della stanza
            rooms[roomId] = rooms[roomId].filter(user => user !== username);

            // Messaggio di aggiornamento per tutti gli utenti nella stanza
            const userlist = {
                type: "userlist",
                users: rooms[roomId],
                roomId: roomId
            };

            // Manda l'aggiornamento a tutti i client connessi nella stanza
            wss.clients.forEach(function each(client) {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify(userlist));
                }
            });
        }
    });
});
