const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 3000 });
const rooms = {};

wss.on('connection', (ws) => {
    ws.on('message', (message) => {
        try {
            const msg = JSON.parse(message);

            if (msg.joinRoom) {
                const uuid = msg.joinRoom;
                ws.uuid = uuid;

                if (!rooms[uuid]) {
                    rooms[uuid] = [];
                }
                rooms[uuid].push(ws);
                console.log(`Client joined room ${uuid}`);
            }

            if (msg.startGame && msg.room) {
                const uuid = msg.room;
                console.log(`Start game in room: ${uuid}`);

                if (rooms[uuid]) {
                    rooms[uuid].forEach(client => {
                        if (client.readyState === WebSocket.OPEN && client.uuid === uuid) {
                            client.send(JSON.stringify({ startGame: true }));
                        }
                    });
                }
            }

        } catch (err) {
            console.error("Errore parsing messaggio:", err.message);
        }
    });

    ws.on('close', () => {
        if (ws.uuid && rooms[ws.uuid]) {
            rooms[ws.uuid] = rooms[ws.uuid].filter(client => client !== ws);
            if (rooms[ws.uuid].length === 0) delete rooms[ws.uuid];
        }
    });
});
