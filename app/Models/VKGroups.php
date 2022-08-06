<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VKGroups extends Model
{
    use HasFactory;
    protected $table = 'vk_groups';
    protected $fillable = ['id','vk_group_id', 'telegram_channel','in_work'];
}
