<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkUsers extends Model
{
    use HasFactory;
    protected $table = 'vk_users';
    protected $fillable = ['id','user_id', 'technology', 'user_status', 'write'];
}
