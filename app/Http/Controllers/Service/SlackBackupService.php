<?php

namespace App\Http\Controllers\Service;

use App\ChannelMembers;
use App\Channels;
use App\Conversations;
use App\User;

class SlackBackupService
{
    private $slack;

    public function __construct(SlackService $slack)
    {
        $this->slack = $slack;
    }

    public function storeUsers()
    {
        $users = $this->slack->listUser();
        foreach ($users as $user) {
            if (!User::find($user->id))
                User::Create([
                    'id' => $user->id,
                    'username' => $user->name,
                    'email' => $user->id . "@ozbul.com",
                    'real_name' => $user->profile->real_name,
                    'avatar' => $user->profile->image_512,
                    'color' => (isset($user->color) == true) ? $user->color : "black",
                ]);
        }
    }

    public function storeMainChannels()
    {
        $channels = $this->slack->ListChannels("public_channel,private_channel");
        foreach ($channels as $channel) {
            if (!Channels::find($channel->id))
                Channels::create(['name' => $channel->name, 'id' => $channel->id, 'is_private' => ($channel->is_im && $channel->is_mpim && $channel->is_private)]);
            $this->storeChannelsMembers($channel->id);
        }
    }

    public function storePrivateChannels() // direct message and group
    {
        $channels = $this->slack->ListChannels("mpim,im");
        foreach ($channels as $channel) {
            if (!Channels::find($channel->id)) {
                Channels::create([
                    'name' => (isset($channel->name) == true) ? $channel->name : User::find($channel->user)->real_name,
                    'id' => $channel->id,
                    'is_private' => true,
                    'is_im' => $channel->is_im,
                    'is_mpim' => !$channel->is_im,
                ]);
            }
            $this->storeChannelsMembers($channel->id);
        }
    }

    public function storeChannelsMembers($channelId)
    {
        $users = $this->slack->retrieveChannelMembers($channelId);
        foreach ($users as $user) {
            ChannelMembers::firstOrCreate(['user_id' => $user, 'channel_id' => $channelId]);
        }
    }

    public function storeConversations($channelId)
    {
        $messages = $this->slack->retrieveMessages($channelId);
        foreach ($messages as $msg) {
            if (isset($msg->client_msg_id) && !Conversations::withTrashed()->where('client_msg_id',$msg->client_msg_id)->exists()) {
                $message = ['client_msg_id' => $msg->client_msg_id, 'type' => $msg->type, 'text' => $msg->text, 'user' => $msg->user, 'ts' => $msg->ts, 'channel_id' => $channelId];
                Conversations::firstOrCreate($message);
            }
        }
    }


    public function BackupEveryThing(){
        $this->storeUsers();
        $this->storeMainChannels();
        $this->storePrivateChannels();

        foreach (Channels::all() as $channel){
            if($channel->is_channel){
                $this->storeConversations($channel->id);
            }
        }
    }


}
