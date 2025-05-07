const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 4000 });

const rooms = {}; // { roomId: { players: { playerId: { ws, closeTimeout } }, currentTurnPlayerId } }

wss.on('connection', (ws) => {
    console.log('🟢 Nuova connessione');

    ws.on('message', (message) => {
        const data = JSON.parse(message);
        console.log('📨 Messaggio ricevuto:', data);

        if (data.joinRoom && data.playerId) {
            const roomId = data.joinRoom;
            const playerId = data.playerId;

            if (!rooms[roomId]) {
                rooms[roomId] = {
                    players: {},
                    currentTurnPlayerId: playerId // Il primo che entra prende il turno
                };
                console.log(`🏠 Stanza ${roomId} creata, turno a ${playerId}`);
            }

            // Se esiste già, cancella il timeout di chiusura
            if (rooms[roomId].players[playerId]?.closeTimeout) {
                clearTimeout(rooms[roomId].players[playerId].closeTimeout);
                console.log(`⏱️ Timeout chiusura annullato per ${playerId}`);
            }

            // Aggiunge o aggiorna il giocatore
            rooms[roomId].players[playerId] = { ws };
            ws.roomId = roomId;
            ws.playerId = playerId;

            console.log(`👤 Giocatore ${playerId} aggiunto/aggiornato nella stanza ${roomId}`);

            // Invia solo lo stato corrente al nuovo arrivato
            sendRoomStateToPlayer(roomId, playerId);
        }

        if (data.turnEnd && data.room && data.playerId) {
            const roomId = data.room;
            const playerId = data.playerId;

            const room = rooms[roomId];
            if (room && room.currentTurnPlayerId === playerId) {
                const playerIds = Object.keys(room.players);
                const otherPlayerId = playerIds.find(id => id !== playerId);

                if (otherPlayerId) {
                    room.currentTurnPlayerId = otherPlayerId;
                    console.log(`🔄 Turno passato a ${otherPlayerId} nella stanza ${roomId}`);

                    broadcastRoomState(roomId);
                }
            }
        }
    });

    ws.on('close', () => {
        const roomId = ws.roomId;
        const playerId = ws.playerId;

        console.log(`❌ Connessione chiusa per ${playerId}`);

        if (roomId && rooms[roomId] && rooms[roomId].players[playerId]) {
            // Aspetta qualche secondo prima di rimuovere davvero il player
            rooms[roomId].players[playerId].closeTimeout = setTimeout(() => {
                console.log(`🕳️ Timeout scaduto: rimuovo ${playerId} dalla stanza ${roomId}`);

                delete rooms[roomId].players[playerId];

                const remainingPlayers = Object.keys(rooms[roomId].players);

                if (remainingPlayers.length === 0) {
                    delete rooms[roomId];
                    console.log(`🗑️ Stanza ${roomId} eliminata (vuota)`);
                } else {
                    // Se il giocatore che aveva il turno esce, passa all’altro
                    if (rooms[roomId].currentTurnPlayerId === playerId) {
                        rooms[roomId].currentTurnPlayerId = remainingPlayers[0];
                        console.log(`⚠️ Turno riassegnato a ${remainingPlayers[0]}`);

                        broadcastRoomState(roomId);
                    }
                }
            }, 3000); // aspetta 3 secondi prima di agire
        }
    });

    ws.on('error', (error) => {
        console.error(`❗ Errore WebSocket:`, error);
    });
});

function broadcastRoomState(roomId) {
    const room = rooms[roomId];
    if (!room) return;

    Object.entries(room.players).forEach(([id, player]) => {
        const client = player.ws;
        if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify({
                turn: id === room.currentTurnPlayerId
            }));
        }
    });
}

function sendRoomStateToPlayer(roomId, playerId) {
    const room = rooms[roomId];
    const client = room?.players[playerId]?.ws;

    if (client && client.readyState === WebSocket.OPEN) {
        client.send(JSON.stringify({
            turn: playerId === room.currentTurnPlayerId
        }));
    }
}

console.log("🚀 Server WebSocket avviato sulla porta 4000");
