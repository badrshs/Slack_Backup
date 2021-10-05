<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://unpkg.com/tailwindcss@0.3.0/dist/tailwind.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"/>
    <style>
        .channel_list:hover {
            background: #1b4b72;
        }

        .button {
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            color: white;
        }


        .button:hover {
            color: #96f2ff;
        }
    </style>
</head>
<body>
<div class="font-sans antialiased h-screen flex">
    <div class="bg-indigo-darker text-purple-lighter flex-none w-64 pb-6 hidden md:block">
        <div class="text-white mb-2 mt-3 px-4 flex justify-between">
            <div class="flex-auto">
                <h1 class="font-semibold text-xl leading-tight mb-1 truncate">Slack Backup</h1>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <div class="flex items-center mb-6">
                    <span class="bg-green rounded-full block w-2 h-2 mr-2"></span>
                    <span class="text-white opacity-50 text-sm">{{auth()->user()->username}}</span>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       style="text-decoration: none; color: white; margin-left: 31px; background: #009688b3; padding: 5px; border-radius: 6px;"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                </div>
                <h4>Total BackedUp Messages </h4>
                <span style="padding: 20px; font-size: 30px; font-weight: 700; color: #E91E63; text-align: center;">
        {{\App\Conversations::all()->count()}} <small style="font-size: 40%;">messages</small>

    </span>
                <br>
                <br>
                <?php  ?>
                last backup : {{\App\Conversations::OrderBy('created_at', 'desc')
                    ->limit(1)->first()->created_at ?? "None"}}
                <hr>
            </div>
            <div>
                <svg class="h-6 w-6 fill-current text-white opacity-25" viewBox="0 0 20 20">
                    <path
                        d="M14 8a4 4 0 1 0-8 0v7h8V8zM8.027 2.332A6.003 6.003 0 0 0 4 8v6l-3 2v1h18v-1l-3-2V8a6.003 6.003 0 0 0-4.027-5.668 2 2 0 1 0-3.945 0zM12 18a2 2 0 1 1-4 0h4z"
                        fill-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <div class="mb-8 boxChild" style="height: 275px;overflow-y: scroll;">
            @foreach($channels as $channel)
                @if(!$channel->has_auth || !$channel->is_channel)
                    @continue
                @endif
                <div style="margin-left: 5px;"
                     class="py-1 channel_list @if($current_channel->id==$channel->id) bg-teal-dark @endif  "
                     @if($current_channel->id==$channel->id) id="scroll-to" @endif >
                    @if(sizeof($channel->chats)==0)
                        <a href="{{route('backup.channel',$channel->id)}}" class="button px-1"
                           style="border-radius: 16px;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </a>
                    @endif
                    <a href="{{route('channel',$channel->id)}}" class="py-1  px-1 text-white"
                       style="text-decoration: none  ">{{$channel->name}}</a>
                </div>

            @endforeach
        </div>
        <div class="mb-8 boxChild " style="height: 350px; overflow-y: scroll;">
            <div class="px-4 mb-2 text-white flex justify-between items-center">
                <div class="opacity-75">Direct Messages</div>
            </div>
            @foreach($channels as $channel)
                @if(!$channel->has_auth | !$channel->is_direct)
                    @continue
                @endif
                <div class="py-1 channel_list @if($current_channel->id==$channel->id) bg-teal-dark @endif  "
                     @if($current_channel->id==$channel->id) id="scroll-to" @endif>

                    @if(sizeof($channel->chats)==0)
                        <a href="{{route('backup.channel',$channel->id)}}" class="button px-1"
                           style="border-radius: 16px;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </a>
                    @endif
                    <a href="{{route('channel',$channel->id)}}" class="py-1 px-1 text-white"
                       style="text-decoration: none  ">{{$channel->name}}</a>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Chat content -->
    <div class="flex-1 flex flex-col bg-white overflow-hidden">
        <!-- Top bar -->
        <div class="border-b flex px-6 py-2 items-center flex-none">
            <div class="flex flex-col">
                <h3 class="text-grey-darkest mb-1 font-extrabold"># {{$current_channel->name}}</h3>
            </div>

            <div class="ml-auto hidden md:block">
                <a href="{{route('backup.channel',$current_channel->id)}}" class="button px-1"
                   style="border-radius: 16px; font-size: 20px; color: #2196F3;;">
                    <i class="fas fa-cloud-upload-alt"></i>
                </a>
            </div>
            <div class="ml-auto hidden md:block">
                <div class="relative">
                    <input type="search" placeholder="Search"
                           class="appearance-none border border-grey rounded-lg pl-8 pr-4 py-2">
                    <div class="absolute pin-y pin-l pl-3 flex items-center justify-center">
                        <svg class="fill-current text-grey h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20">
                            <path
                                d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chat messages -->
        <div class="px-6 py-4 flex-1 overflow-y-scroll" id="all_messages" style="  ;">
            @foreach($current_channel->chats as $message)

                <div class="flex items-start mb-4 text-sm">
                    <img src="{{$message->owner->avatar}}" class="w-10 h-10 rounded mr-3">
                    <div class="flex-1 overflow-hidden">
                        <div>
                            <span class="font-bold">{{$message->owner->real_name}}</span>
                            <span class="text-grey text-xs">{{date('m-d-Y h:i',$message->ts)}}</span>
                            @if($message->user== auth()->id())
                                <a href="{{route('message.delete',$message->client_msg_id)}}">
                                    <i class="fas fa-trash-alt" style="color: #F44336;"></i></a>
                            @endif
                        </div>
                        <p class="text-black leading-normal text-message">{{$message->text}}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    var element = document.getElementById('all_messages');
    element.scrollTop = element.scrollHeight - element.clientHeight;

    var users = @json(\App\User::all());


    var messages = document.getElementsByClassName("text-message");

    [].forEach.call(messages, function (el) {

        var string = el.innerHTML;
        const regex = /&lt;@.*?&gt/g;
        const found = string.match(regex);
        console.log(string)
        if (found != null)
            found.forEach(function (item) {
                var id = item.replace("&lt;@", "");
                id = id.replace("&gt", "");
                string = string.replace(item, "<span style='color:blue'>@" + getUsernameById(id) + "</span>");
            });
        el.innerHTML = string;
    });


    function getUsernameById(id) {
        console.log(id)
        return users.find(element => element['id'] == id)["username"];
    }

</script>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/fontawesome.min.js"
        integrity="sha256-7zqZLiBDNbfN3W/5aEI1OX/5uvck9V0yhwKOA9Oe49M=" crossorigin="anonymous"></script>
</html>
