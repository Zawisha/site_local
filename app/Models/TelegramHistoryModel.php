<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramHistoryModel extends Model
{
    protected $table = 'telegram_history';
    protected $fillable = ['id','channel', 'last_id','in_work'];
}
