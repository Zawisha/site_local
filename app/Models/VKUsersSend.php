<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VKUsersSend extends Model
{
    use HasFactory;
    protected $table = 'vk_users_send';
    protected $fillable = ['id','user_id', 'technology_id','message_number','is_closed','vk_number'];
}
