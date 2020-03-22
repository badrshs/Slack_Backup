<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelMembers extends Model
{
    protected $fillable = ['user_id','channel_id'];
}
