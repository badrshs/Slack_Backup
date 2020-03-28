<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversations extends Model
{
    protected $primaryKey = "client_msg_id";
    public $fillable = ['client_msg_id', 'type', 'text', 'user', 'ts', 'channel_id'];
    protected $keyType = 'string';
    use SoftDeletes;


    public function owner(){
        return $this->belongsTo(User::class,'user','id');
    }

}
