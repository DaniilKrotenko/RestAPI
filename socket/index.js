
require('dotenv').config({ path: '../.env' });
var express = require('express');
var app = express();
var http = require('http').Server(app);
var io = require('socket.io')(http, {cors: {
        origin: "*"
    }});

var mysql = require('mysql2');
var moment = require('moment');
var sockets = {};
var con = mysql.createConnection({
    host: process.env.DB_HOST,
    port: process.env.DB_PORT,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE
});



con.connect(function (err) {
    if (err)
        throw err;
    console.log("Database Connected")
});

io.on('connection', function (socket) {
    if (!sockets[socket.handshake.query.user_id]) {
        sockets[socket.handshake.query.user_id] = [];
    }
    sockets[socket.handshake.query.user_id].push(socket);
    socket.broadcast.emit('user_connected', socket.handshake.query.user_id);

    con.query('UPDATE users SET active = 1 where id="'+socket.handshake.query.user_id+'"', function (err, res) {
        if (err)
            throw err;
        console.log("User Connected", socket.handshake.query.user_id);
    })

    socket.on('send_message', function (data){
        group_id = (data.user_id>data.other_user_id)?data.user_id+'.'+data.other_user_id:data.other_user_id+'.'+data.user_id;
        var timeNow = moment().format("h:mm A");
        data.time = timeNow;

        con.query(`INSERT INTO notification (from_user,to_user,text,types,group_id,created_at) values (${data.user_id}, ${data.other_user_id}, "${data.text.replace('"', "'")}", '${data.types}', ${group_id}, CURRENT_TIMESTAMP)`, function (err,res){
            if(err)
                throw err;
            data.id = res.insertId;
            for(var index in sockets[data.user_id]){
                sockets[data.user_id][index].emit('receive_message', data);
            }
            con.query(`SELECT COUNT(id) as unread_messages from notification where from_user=${data.user_id} and to_user=${data.other_user_id} and is_read=0`, function (err, res){
                if (err)
                    throw err;
                data.unread_messages = res[0].unread_messages;
                for(var index in sockets[data.other_user_id]){
                    sockets[data.other_user_id][index].emit('receive_message', data);
                }
            })
        })
    });

    socket.on('read_message', function(id){
        con.query(`UPDATE notification set is_read=1 where id=${id}`, function (err,res){
            if(err)
                throw err;
            console.log('Message read');
        })
    })

    socket.on('user_typing',function(data){
        // console.log(data);
        for(var index in sockets[data.other_user_id]){
            sockets[data.other_user_id][index].emit('user_typing',data);
        }
    })

    socket.on('disconnect', function (err) {
        socket.broadcast.emit('user_disconnected', socket.handshake.query.user_id);
        for(var index in sockets[socket.handshake.query.user_id]) {
            if (sockets.id == sockets[socket.handshake.query.user_id][index].id) {
                sockets[socket.handshake.query.user_id].splice(index,1);
            }
        }

        con.query('UPDATE users SET active = 0 where id="'+socket.handshake.query.user_id+'"', function (err, res) {
            if (err)
                throw err;
            console.log('User Disconnected', socket.handshake.query.user_id);
        })
    })

})

http.listen(3000);