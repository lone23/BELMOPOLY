const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 3000 });

const rooms = {}; // { uuid: [ws, ws, ...] }
const turni = {}; // { uuid: { players: [ws1, ws2, ...], current: 0 } }

wss.on('connection', (ws) => {
    ws.on('message', (message) => {
        try {
            const msg = JSON.parse(message);

            // JOIN ROOM
            if (msg.joinRoom) {
                const uuid = msg.joinRoom;
                ws.uuid = uuid;

                if (!rooms[uuid]) {
                    rooms[uuid] = [];
                    console.log(`Creata nuova stanza con uuid: ${uuid}`);
                }

                rooms[uuid].push(ws);
                console.log(`Client joined room ${uuid}. Current players: ${rooms[uuid].length}`);
            }

            // START GAME
            if (msg.startGame && msg.room) {
                const uuid = msg.room;
                console.log(`Start game request received for room: ${uuid}`);

                if (rooms[uuid]) {
                    turni[uuid] = {
                        players: rooms[uuid],
                        current: msg.startingIndex || 0
                    };

                    console.log(`Game started in room ${uuid}. Current turn player: ${turni[uuid].current}`);

                    // Informa tutti che il gioco è partito
                    rooms[uuid].forEach((client, index) => {
                        if (client.readyState === WebSocket.OPEN) {
                            client.send(JSON.stringify({
                                startGame: true,
                                turno: index === turni[uuid].current
                            }));
                            console.log(`Sent start game to player ${index} in room ${uuid}`);
                        }
                    });
                }
            }

            // AVVISO: TURNO SUCCESSIVO
            if (msg.turnFinished && msg.room) {
                const uuid = msg.room;
                console.log(`Turn finished request received for room: ${uuid}`);

                if (turni[uuid]) {
                    const roomData = turni[uuid];
                    roomData.current = (roomData.current + 1) % roomData.players.length;

                    console.log(`Turn changed in room ${uuid}. Current turn player: ${roomData.current}`);

                    roomData.players.forEach((client, index) => {
                        if (client.readyState === WebSocket.OPEN) {
                            client.send(JSON.stringify({
                                nextTurn: true,
                                turno: index === roomData.current
                            }));
                            console.log(`Sent next turn to player ${index} in room ${uuid}`);
                        }
                    });
                }
            }

        } catch (err) {
            console.error("Errore parsing messaggio:", err.message);
        }
    });

    ws.on('close', () => {
        const uuid = ws.uuid;
        if (uuid && rooms[uuid]) {
            rooms[uuid] = rooms[uuid].filter(client => client !== ws);
            console.log(`Client left room ${uuid}. Current players: ${rooms[uuid].length}`);

            if (rooms[uuid].length === 0) {
                delete rooms[uuid];
                delete turni[uuid];
                console.log(`Room ${uuid} has no players left. Room deleted.`);
            }
        }
    });
});
