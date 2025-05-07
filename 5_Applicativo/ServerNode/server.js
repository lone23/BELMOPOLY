const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 3000 });

let rooms = {}; // Oggetto per memorizzare le stanze e i loro client

wss.on('connection', ws => {
    console.log("Un client si è connesso a WebSocket");

    // Logga l'ID del client per tracciare meglio chi si connette
    const clientId = Math.random().toString(36).substring(7); // Genera un ID temporaneo per il client
    console.log(`ID Client: ${clientId} - Connessione WebSocket avvenuta`);

    // Gestisce i messaggi in arrivo
    ws.on('message', message => {
        console.log(`ID Client: ${clientId} - Messaggio ricevuto: ${message}`);
        const data = JSON.parse(message);

        // Gestione dell'operazione di unione alla stanza
        if (data.joinRoom) {
            const room = data.joinRoom;
            if (!rooms[room]) {
                rooms[room] = [];  // Se la stanza non esiste, la creiamo
                console.log(`ID Client: ${clientId} - Stanza ${room} creata.`);
            }
            rooms[room].push(ws); // Aggiungi il client alla stanza
            console.log(`ID Client: ${clientId} - Client aggiunto alla stanza ${room}. Client tot: ${rooms[room].length}`);
        }

        // Gestisce il comando per avviare il gioco
        if (data.startGame && data.room) {
            console.log(`ID Client: ${clientId} - Tentativo di avviare il gioco nella stanza ${data.room}`);

            // Trova la stanza e invia il messaggio di avvio a tutti i client della stanza
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

    // Gestione della chiusura della connessione
    ws.on('close', () => {
        console.log(`ID Client: ${clientId} - Connessione chiusa`);

        // Rimuovi il client dalle stanze
        for (let room in rooms) {
            rooms[room] = rooms[room].filter(client => client !== ws);
        }
        console.log(`ID Client: ${clientId} - Client disconnesso e rimosso dalle stanze`);
    });
});

console.log("Server WebSocket in esecuzione sulla porta 3000");
