const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: '*',           
        methods: ['GET', 'POST']
    }
});

// Endpoint yang dipanggil backend PDCA
app.post('/broadcast', express.json(), (req, res) => {
    const payload = req.body;

    // Verifikasi secret biar tidak sembarang orang bisa broadcast
    if (req.headers['x-broadcast-secret'] !== 'abogobogahesoyam') {
        return res.status(401).json({ error: 'unauthorized' });
    }

    io.emit('stats.updated', payload);
    console.log('[broadcast]', new Date().toISOString(), payload);
    res.json({ ok: true });
});

// Health check
app.get('/health', (req, res) => res.json({ ok: true, clients: io.engine.clientsCount }));

io.on('connection', (socket) => {
    console.log('[ws] client connected:', socket.id);
    socket.on('disconnect', () => console.log('[ws] disconnected:', socket.id));
});

server.listen(3002, '10.203.68.47', () => {
    console.log('WS server listening on 10.203.68.47:3002');
});