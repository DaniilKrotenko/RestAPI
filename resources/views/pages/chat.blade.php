@extends('layouts.app')

@section('content')

<div id="snippetContent">
    <main class="content">
        <div class="container p-0">
            <div class="card">
                <div class="row g-0">
                    <div class="col-12 col-lg-5 col-xl-3 border-right">
                        @foreach($friends as $friend)
                        <a href="{{route('chat', $friend['id'])}}" class="list-group-item list-group-item-action border-0">
                            <div id="unread-count-{{$friend['id']}}">
                                @if($friend['unread_messages']>0)
                                <div class="badge bg-success float-right">{{$friend['unread_messages']}}</div>
                                @endif
                            </div>
                            <div class="d-flex align-items-start">
                                <img src="https://ui-avatars.com/api/?name={{$friend['first_name'].' '.$friend['last_name']}}"
                                     class="rounded-circle mr-1" alt="Vanessa Tucker" width="40" height="40"/>
                                <div class="flex-grow-1 ml-3">
                                    {{$friend['first_name'].' '.$friend['last_name']}}
                                    <div class="small" id="status_{{$friend['id']}}">
                                        @if($friend['active'] == 1)
                                        <span class="fa fa-circle chat-online"></span>
                                        Online
                                        @else
                                        <span class="fa fa-circle chat-offline"></span>
                                        Offline
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                        <hr class="d-block d-lg-none mt-1 mb-0"/>
                        </div>
                        <div class="col-12 col-lg-7 col-xl-9">
                            @if($id)
                            <div class="py-2 px-4 border-bottom d-none d-lg-block">
                                <div class="d-flex align-items-center py-1">
                                    <div class="position-relative"><img
                                                src="https://ui-avatars.com/api/?name={{$otherUser->first_name.' '.$otherUser->last_name}}"
                                                class="rounded-circle mr-1" alt="Sharon Lessman" width="40"
                                                height="40"/>
                                    </div>
                                    <div class="flex-grow-1 pl-3">
                                        <strong>{{$otherUser->first_name.' '.$otherUser->last_name}}</strong>
                                        <div class="text-muted small" id="typing-in"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative">
                                <div class="chat-messages p-4">
                                    @foreach($messages as $message)
                                    @if($message['from_user'] == \Auth::id())
                                    <div class="chat-message-right pb-4">
                                        <div>
                                            <img src="https://ui-avatars.com/api/?name={{\Auth::user()->first_name.' '.\Auth::user()->last_name}}"
                                                 class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40"/>
                                            <div class="text-muted small text-nowrap mt-2">{{date('h:i A',
                                                strtotime($message['created_at']))}}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                            <div class="font-weight-bold mb-1">You</div>
                                            {{$message['text']}}
                                        </div>
                                    </div>
                                    @else
                                    <div class="chat-message-left pb-4">
                                        <div>
                                            <img src="https://ui-avatars.com/api/?name={{$otherUser->first_name.' '.$otherUser->last_name}}"
                                                 class="rounded-circle mr-1" alt="Sharon Lessman" width="40"
                                                 height="40"/>
                                            <div class="text-muted small text-nowrap mt-2">{{date('h:i A',
                                                strtotime($message['created_at']))}}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                                            <div class="font-weight-bold mb-1">{{$otherUser->first_name}}
                                            </div>
                                            {{$message['text']}}
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex-grow-0 py-3 px-4 border-top">
                                <form id="chat-form">
                                    <div class="input-group">
                                        <input type="text" id="message-input" class="form-control" name="message"
                                               placeholder="Type your message"/>
                                        <button class="btn btn-primary" type="submit">Send</button>
                                    </div>
                                </form>
                            </div>
                            @else

                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </main>
</div>
</div>

@endsection

@section('scripts')
<script>
    var timeOut;
    $(function () {


        var other_user_id = "{{($otherUser)?$otherUser->id:''}}";
        var otherUserName = "{{($otherUser)?$otherUser->first_name:''}}";



        var time = new Date;
        var timeNow = time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });

        $(".chat-messages").animate({scrollTop: $(".chat-messages").prop("scrollHeight")}, 1000);
        $("#chat-form").on("submit", function (e) {
            e.preventDefault();
            var text = $('#message-input').val();
            if (text.trim().length == 0) {
                $("#message-input").focus();
            } else {
                var data = {
                    user_id: user_id,
                    other_user_id: other_user_id,
                    text: text,
                    types: 'message',
                    otherUserFirstName: '{{\Auth::user()->first_name}}',
                    otherUserLastName: '{{\Auth::user()->last_name}}',
                    time: timeNow
                }
                socket.emit('send_message', data);
                $('#message-input').val('');
            }
        })

        socket.on('user_connected', function (data) {
            $("#status_" + data).html('<span class="fa fa-circle chat-online"></span> Online');
        });

        socket.on('user_disconnected', function (data) {
            $("#status_" + data).html('<span class="fa fa-circle chat-offline"></span> Offline');
        })

        socket.on('receive_message', function (data) {
            if ((data.user_id == user_id && data.other_user_id == other_user_id) || (data.user_id == other_user_id && data.other_user_id == user_id)) {
                if (data.user_id == user_id) {
                    var html = `<div class="chat-message-right pb-4">
                                    <div>
                                        <img src="https://ui-avatars.com/api/?name={{\Auth::user()->first_name.' '.\Auth::user()->last_name}}"
                                             class="rounded-circle mr-1" alt="Chris Wood" width="40" height="40"/>
                                        <div class="text-muted small text-nowrap mt-2">${data.time}</div>
                                    </div>
                                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                        <div class="font-weight-bold mb-1">You</div>
                                        ${data.text}
                                    </div>
                                </div>`
                } else {
                    socket.emit('read_message', data.id);
                    var html = `<div class="chat-message-left pb-4">
                                    <div>
                                        <img src="https://ui-avatars.com/api/?name=${data.otherUserFirstName}+' '+${data.otherUserLastName}" class="rounded-circle mr-1" alt="Sharon Lessman" width="40" height="40"/>
                                        <div class="text-muted small text-nowrap mt-2">${data.time}</div>
                                    </div>
                                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                                        <div class="font-weight-bold mb-1">${data.otherUserFirstName}</div>
                                        ${data.text}
                                    </div>
                                </div>`
                }

                $(".chat-messages").append(html);
                $(".chat-messages").animate({scrollTop: $(".chat-messages").prop("scrollHeight")}, 1000);
            } else {
                $('#unread-count-'+data.user_id).html(`<div class="badge bg-success float-right">`+data.unread_messages+`</div>`);
            }
        })

        socket.on('user_typing', function(data){
            console.log("user_typing",data);
            if(data.user_id == other_user_id){
                $('#typing-in').html('<em>Typing</em>');
                clearTimOut();
                clearTyping();
            }
        });

        $("#message-input").on('keyup', function (){
            socket.emit('user_typing',{user_id:user_id, other_user_id:other_user_id});
        });
    });


    function clearTyping(){
       timeOut =  setTimeout(function (){
            $('#typing-in').html('');
        }, 3000);
    }

function clearTimOut(){
        clearTimeout(timeOut);
}

</script>
@endsection
