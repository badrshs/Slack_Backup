
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://unpkg.com/tailwindcss@0.3.0/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .channel_list:hover{
            background: #38a89d;
        }
        .button {
            background-color: #4CAF50; /* Green */
            padding: 16px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            color: black;
            border: 2px solid #2196F3;
        }



        .button:hover {
            background-color: #0c16f3;
            color: black;
        }
    </style>
</head>
<body  style="background: #edf2f7;">
<div class="font-sans antialiased h-screen flex">
    <div class="bg-indigo-darker text-purple-lighter flex-none w-64 pb-6 hidden md:block">
        <div class="text-white mb-2 mt-3 px-4 flex justify-between">
            <div class="flex-auto">
                <h1 class="font-semibold text-xl leading-tight mb-1 truncate">Slack Backup</h1>
                <div class="flex items-center mb-6">
                    <span class="bg-green rounded-full block w-2 h-2 mr-2"></span>
                    <span class="text-white opacity-50 text-sm">{{auth()->user()->username}}</span>
                </div>
            </div>
            <div>
                <svg class="h-6 w-6 fill-current text-white opacity-25" viewBox="0 0 20 20">
                    <path d="M14 8a4 4 0 1 0-8 0v7h8V8zM8.027 2.332A6.003 6.003 0 0 0 4 8v6l-3 2v1h18v-1l-3-2V8a6.003 6.003 0 0 0-4.027-5.668 2 2 0 1 0-3.945 0zM12 18a2 2 0 1 1-4 0h4z" fill-rule="evenodd" />
                </svg>
            </div>
        </div>
        <div class="mb-8" style="height: 350px; overflow-y: scroll;">
            @foreach($channels as $channel)
                @if(!$channel->has_auth || !$channel->is_channel)
                    @continue
                @endif
            <div class="py-1 channel_list @if($current_channel->id==$channel->id) bg-teal-dark @endif  ">
                <a href="{{route('channel',$channel->id)}}" class="py-1 px-4 text-white" style="text-decoration: none  ">{{$channel->name}}</a>
                @if(sizeof($channel->chats)==0)
                    <a href="{{route('backup.channel',$channel->id)}}" class="button" style="background: white; border-radius: 16px; padding: 5px;" >Backup</a>
                @endif
            </div>

            @endforeach
        </div>
        <div class="mb-8 " style="height: 350px; overflow-y: scroll;">
            <div class="px-4 mb-2 text-white flex justify-between items-center">
                <div class="opacity-75">Direct Messages</div>
            </div>
          @foreach($channels as $channel)
                @if(!$channel->has_auth | !$channel->is_direct)
                    @continue
                @endif
                <div class="py-1 channel_list @if($current_channel->id==$channel->id) bg-teal-dark @endif  ">
                    <a href="{{route('channel',$channel->id)}}" class="py-1 px-4 text-white" style="text-decoration: none  ">{{$channel->name}}</a>
                   @if(sizeof($channel->chats)==0)
                    <a href="{{route('backup.channel',$channel->id)}}" class="button" style="background: white; border-radius: 16px; padding: 5px;" >Backup</a>
                    @endif
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
                <div class="relative">
                    <input type="search" placeholder="Search" class="appearance-none border border-grey rounded-lg pl-8 pr-4 py-2">
                    <div class="absolute pin-y pin-l pl-3 flex items-center justify-center">
                        <svg class="fill-current text-grey h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chat messages -->
        <div class="px-6 py-4 flex-1 overflow-y-scroll">
         @foreach($current_channel->chats as $message)

            <div class="flex items-start mb-4 text-sm">
                <img src="{{$message->owner->avatar}}" class="w-10 h-10 rounded mr-3">
                <div class="flex-1 overflow-hidden">
                    <div>
                        <span class="font-bold">{{$message->owner->real_name}}</span>
                        <span class="text-grey text-xs">{{date('m-d-Y h:i',$message->ts)}}</span>
                    </div>
                    <p class="text-black leading-normal">{{$message->text}}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
