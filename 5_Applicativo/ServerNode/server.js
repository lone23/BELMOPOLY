const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 3000 });

let rooms = {};

wss.on('connection', ws => {
    console.log("Un client si è connesso a WebSocket");

    const clientId = Math.random().toString(36).substring(7); // Genera un ID temporaneo per il client
    console.log(`ID Client: ${clientId} - Connessione WebSocket avvenuta`);

    ws.on('message', message => {
        console.log(`ID Client: ${clientId} - Messaggio ricevuto: ${message}`);
        const data = JSON.parse(message);

        if (data.joinRoom) {
            const room = data.joinRoom;
            if (!rooms[room]) {
                rooms[room] = [];
                console.log(`ID Client: ${clientId} - Stanza ${room} creata.`);
            }
            rooms[room].push(ws);
            console.log(`ID Client: ${clientId} - Client aggiunto alla stanza ${room}. Client tot: ${rooms[room].length}`);
        }

        if (data.startGame && data.room) {
            console.log(`ID Client: ${clientId} - Tentativo di avviare il gioco nella stanza ${data.room}`);

            if (rooms[data.room] && rooms[data.room].length > 0) {
                console.log(`ID Client: ${clientId} - Stanza trovata, avvio del gioco per ${rooms[data.room].length} client`);

                rooms[data.room].forEach(client => {
                    client.send(JSON.stringify({
                        startGame: true,
                        room: data.room,
                        startingIndex: data.startingIndex
                    }));
                });
            } else {
                console.log(`ID Client: ${clientId} - Stanza ${data.room} non trovata o vuota. Impossibile avviare il gioco.`);
            }
        }
    });

    ws.on('close', () => {
        console.log(`ID Client: ${clientId} - Connessione chiusa`);


        for (let room in rooms) {
            rooms[room] = rooms[room].filter(client => client !== ws);
        }
        console.log(`ID Client: ${clientId} - Client disconnesso e rimosso dalle stanze`);
    });
});

console.log("Server WebSocket in esecuzione sulla porta 3000");
