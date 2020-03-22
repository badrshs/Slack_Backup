<?php

namespace App\Http\Controllers;

use App\ChannelMembers;
use App\Channels;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $channel = Channels::where('is_private', 1)->first();

        return redirect()->route('channel', $channel->id);
    }

    public function channel(Channels $channel)
    {
       if(!$channel->has_auth)
           abort(401);
        $data['channels'] = Channels::get();
        $data['current_channel'] = $channel;
        return view('home', $data);
    }
}
