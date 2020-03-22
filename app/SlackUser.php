<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SlackUser extends Model
{
    protected $keyType = 'string';

    protected $fillable = ['id','name','color','image','email'];
}
