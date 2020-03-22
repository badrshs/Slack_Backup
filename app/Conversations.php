<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversations extends Model
{
    public $fillable = ['client_msg_id', 'type', 'text', 'user', 'ts', 'channel_id'];
    protected $keyType = 'string';

    public function owner(){
        return $this->belongsTo(User::class,'user','id');
    }

}
