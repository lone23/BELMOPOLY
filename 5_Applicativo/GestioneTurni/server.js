const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 4000 });

const rooms = {}; // { roomId: { players: { playerId: { ws, closeTimeout } }, playerOrder: [], currentTurnPlayerId } }

wss.on('connection', (ws) => {

    ws.on('message', (message) => {
        const data = JSON.parse(message);
        console.log(' Messaggio ricevuto:', data);

        if (data.joinRoom && data.playerId) {
            const roomId = data.joinRoom;
            const playerId = data.playerId;

            if (!rooms[roomId]) {
                rooms[roomId] = {
                    players: {},
                    playerOrder: [],
                    currentTurnPlayerId: playerId 
                };
                console.log(` Stanza ${roomId} creata, turno a ${playerId}`);
            }

            if (rooms[roomId].players[playerId]?.closeTimeout) {
                clearTimeout(rooms[roomId].players[playerId].closeTimeout);
                console.log(` Timeout chiusura annullato per ${playerId}`);
            }

            rooms[roomId].players[playerId] = { ws };
            ws.roomId = roomId;
            ws.playerId = playerId;

            if (!rooms[roomId].playerOrder.includes(playerId)) {
                rooms[roomId].playerOrder.push(playerId);
            }

            console.log(` Giocatore ${playerId} aggiunto/aggiornato nella stanza ${roomId}`);

            sendRoomStateToPlayer(roomId, playerId);
        }

        if (data.turnEnd && data.room && data.playerId) {
            const roomId = data.room;
            const playerId = data.playerId;

            const room = rooms[roomId];
            if (room && room.currentTurnPlayerId === playerId) {
                const order = room.playerOrder;
                const currentIndex = order.indexOf(playerId);
                const nextIndex = (currentIndex + 1) % order.length;
                const nextPlayerId = order[nextIndex];

                room.currentTurnPlayerId = nextPlayerId;
                console.log(` Turno passato a ${nextPlayerId} nella stanza ${roomId}`);

                broadcastRoomState(roomId);
            }
        }
    });

    ws.on('close', () => {
        const roomId = ws.roomId;
        const playerId = ws.playerId;

        console.log(` Connessione chiusa per ${playerId}`);

        if (roomId && rooms[roomId] && rooms[roomId].players[playerId]) {
            rooms[roomId].players[playerId].closeTimeout = setTimeout(() => {
                console.log(` Timeout scaduto: rimuovo ${playerId} dalla stanza ${roomId}`);

                delete rooms[roomId].players[playerId];
                rooms[roomId].playerOrder = rooms[roomId].playerOrder.filter(id => id !== playerId);

                const remainingPlayers = rooms[roomId].playerOrder;

                if (remainingPlayers.length === 0) {
                    delete rooms[roomId];
                    console.log(` Stanza ${roomId} eliminata (vuota)`);
                } else {

                    if (rooms[roomId].currentTurnPlayerId === playerId) {
                        rooms[roomId].currentTurnPlayerId = remainingPlayers[0];
                        console.log(` Turno riassegnato a ${remainingPlayers[0]}`);

                        broadcastRoomState(roomId);
                    }
                }
            }, 3000); 
        }
    });

    ws.on('error', (error) => {
        console.error(` Errore WebSocket:`, error);
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

console.log(" Server WebSocket avviato sulla porta 4000");
