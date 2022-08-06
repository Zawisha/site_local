<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkTimeTelegram extends Model
{
    use HasFactory;
    protected $table = 'time_vk_telegram';
    protected $fillable = ['id','channel', 'time'];
}
