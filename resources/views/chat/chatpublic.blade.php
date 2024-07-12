@extends('layouts.app')

@section('style')
    <style>
        .item img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 5px;
            object-fit: cover;
        }

        .item {
            display: flex;
            padding: 10px;
            align-items: center;
            position: relative;

            color: #1a202c;
            text-decoration: none;

        }

        .item:hover {
            opacity: 0.8;
        }

        .status {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: chartreuse;
            top: 5px;
        }

        .block-chat {
            width: 100%;
            height: 450px;
            border: 1px solid #a0aec0;
            overflow-y: auto;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
        }
        .bg-cus{
            background: #dfe6e9;
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
            overflow-y: auto;
        }
        .bg-cus-2{
            border-radius: 25px;
            background: #a0aec0;
            padding-top: 8px;
            padding-bottom: 3px;
            padding-left: 10px;
            margin-top: 15px;
            margin-bottom: 6px;

        }
        .block-chat li {
            list-style: none;
            margin-top: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
            max-width: 50%;
            padding: 10px;
            border-radius: 15px;
            word-wrap: break-word;
        }
        .my-message {
            color: white;
            background-color: #636e72;
            margin-left: auto;
            text-align: right;
        }
        .other-message {
            background-color: lightgray;
        }

    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-3 bg-cus">
                <div class="bg-cus-2">
                    <h5>Friends</h5>
                </div>
                <div class="row">
                    @foreach($users as $user)
                        <div class="col-md-12 ">
                            <a href="" class="item" id="link_{{$user->id}}">
                                <img src="{{$user->image}}" alt="">
                                <p>{{$user->name}}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-9">
                <div>
                    <h5>Chat</h5>
                </div>
                <div class="">
                    <ul class="block-chat">
                        @foreach($messages as $message)
                            <li class="{{ $message->user_id == Auth::id() ? 'my-message' : 'other-message' }}">
                                {{ $message->user->name }}: {{ $message->message }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form>
                    <div class="d-flex">
                        <input type="text" class="form-control me-2" id="inputChat">
                        <button type="button" class="btn btn-success" id="btnSend">Send</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script type="module">
        Echo.join('chat')
            .here(users => {
                users.forEach(item => {
                    let element = document.querySelector(`#link_${item.id}`)
                    let elementStatus = document.createElement('div')
                    elementStatus.classList.add('status')
                    if (element) {
                        element.appendChild(elementStatus)
                    }
                })
            })
            .joining(user => {
                let element = document.querySelector(`#link_${user.id}`)
                let elementStatus = document.createElement('div')
                elementStatus.classList.add('status')
                if (element) {
                    element.appendChild(elementStatus)
                }
            })
            .leaving(user => {
                let element = document.querySelector(`#link_${user.id}`)
                let elementStatus = element.querySelector('.status')
                if (elementStatus) {
                    element.removeChild(elementStatus)
                }
            })
            .listen('UserOnline', event => {
                let blockChat = document.querySelector(".block-chat")
                let elementChat = document.createElement('li')
                elementChat.textContent = `${event.user.name}: ${event.message}`
                if (event.user.id == "{{Auth::user()->id}}") {
                    elementChat.classList.add('my-message')
                }else {
                    elementChat.classList.add('other-message')
                }
                blockChat.appendChild(elementChat)
                blockChat.scrollTop = blockChat.scrollHeight;
            })

        let inputChat = document.querySelector("#inputChat")
        let btnSend = document.querySelector("#btnSend")

        function sendMessage() {
            let message = inputChat.value;
            if (message.trim() === '') return;

            axios.post('{{ route("sendMessage") }}', {
                'message': message
            })
                .then(data => {
                    console.log(data.data.success);
                    inputChat.value = '';

                    // Display the message immediately in the sender's chat window
                    let blockChat = document.querySelector(".block-chat")
                    let elementChat = document.createElement('li')
                    elementChat.textContent = `{{ Auth::user()->name }}: ${message}`
                    elementChat.classList.add('my-message')
                    blockChat.appendChild(elementChat)
                    blockChat.scrollTop = blockChat.scrollHeight; // Scroll to the bottom
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                });
        }

        btnSend.addEventListener('click', function () {
            sendMessage();
        })

        inputChat.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendMessage();
            }
        })
    </script>
@endsection
