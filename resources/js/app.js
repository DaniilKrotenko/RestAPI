/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

const messages_el = document.getElementById("messages");
const username_input = document.getElementById("username");
const message_input = document.getElementById("message_input");
const message_form = document.getElementById("message_form");

message_form.addEventListener('submit', function (e){
    e.preventDefault()

    let has_errors = false;

    if (username_input.value == ''){
        alert("Please write your username");
        has_errors = true;
    }

    if (message_input.value == ''){
        alert("Please write your message");
        has_errors = true;
    }

    if(has_errors){
        return;
    }

    const options = {
        method: 'post',
        url: '/api/messages',
        data: {
            username: username_input.value,
            message: message_input.value,
        },
        transformResponse: [(data) => {
            return data;
        }]

    }
    axios(options);
});

window.Echo.channel("chat")
    .listen('.message', (e) => {
        console.log(e);
        // messages_el.innerHTML += '<div class="message"><strong>' + e.username + ':</strong>' + e.message + '</div>';
    });

