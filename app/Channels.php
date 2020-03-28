<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channels extends Model
{
    protected $keyType = 'string';

    protected $fillable = ['id','name','is_private','is_im','is_mpim'];
    protected $appends = ['has_auth','is_channel','is_direct'];

    public function chats(){
        return $this->hasMany(Conversations::class,'channel_id','id')->orderBy('ts');
    }

    public function getHasAuthAttribute(){
        return $this->channelMember()->where('user_id', auth()->id())->exists();
    }
    public function getIsChannelAttribute(){
        return !$this->is_private && !$this->is_im  && !$this->is_mpim ;
    }

    public function getIsDirectAttribute(){
        return $this->is_private && ($this->is_im  || $this->is_mpim) ;
    }

    public function channelMember(){
        return $this->hasMany(ChannelMembers::class,'channel_id','id');
    }
}
