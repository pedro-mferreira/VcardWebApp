const httpServer = require('http').createServer()
const io = require("socket.io")(httpServer, {
    allowEIO3: true,
    cors: {
        origin: "http://localhost:8081",
        methods: ["GET", "POST"],
        credentials: true
    }
})
httpServer.listen(8080, function () {
    console.log('listening on *:8080')
})
io.on('connection', function (socket) {
    console.log(`client ${socket.username} has connected`)
    
    socket.on('logged_in', function (user) {
        socket.join(user.username + '')
    })
    socket.on('existTransactions', function (pairTransaction) {
        console.log(pairTransaction)
        io.to(pairTransaction.destiny+ '').emit('newTransactions', pairTransaction.source + ":" + pairTransaction.from)
        
    })

    socket.on('userHasBeenBlocked', function (userBlocked) {
        
        io.to(userBlocked+ '').emit('userBlocked',userBlocked)
        
    })

    socket.on('userHasBeenDeleted', function (userBlocked) {
        
        io.to(userBlocked+ '').emit('userDeleted',userBlocked)
        
    })
    
    socket.on('logged_out', function (user) {
        socket.leave(user.username+'')
    })

    
    socket.on('updateUser', function (user) {
        socket.to('administrator').except(user.id).emit('updateUser', user)
        socket.to(user.id).emit('updateUser', user)
    })
})
