<?php

namespace App\Http\Controllers;

use App\Channels;
use App\Conversations;
use App\Http\Controllers\Service\SlackBackupService;

class SlackController extends Controller
{
    private $slackBackup;

    public function __construct()
    {
        $this->slackBackup = new SlackBackupService();
    }

    public function storeChannelsMembers($channel)
    {
        $this->slackBackup->storeChannelsMembers($channel);

    }

    public function storeConversations(Channels $channel)
    {
        if (!$channel->has_auth)
            return;
        $this->slackBackup->storeConversations($channel->id);

        return redirect()->route('channel', $channel->id);
    }

    public function storeEverything()
    {
        $this->storeUsers();
        $this->storeMainChannels();
        $this->storePrivateChannels();
        $this->storeAllConversations();
    }

    public function storeUsers()
    {
        $this->slackBackup->storeUsers();
    }

    public function storeMainChannels()
    {
        $this->slackBackup->storeMainChannels();
    }

    public function storePrivateChannels() // direct message and group
    {
        $this->slackBackup->storePrivateChannels();

    }

    public function storeAllConversations()
    {
        foreach (Channels::all() as $channel)
            $this->slackBackup->storeConversations($channel->id);
    }

    public function deleteConversations(Conversations $message)
    {
        if (!$message->user == auth()->id())
            abort(401);
        $message->text = "";
        $message->save();
        $message->delete();

        return redirect()->back();
    }
}
