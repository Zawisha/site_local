<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramHistoryChannels extends Model
{
    protected $table = 'telegram_history_channels';
    protected $fillable = ['id','search_word'];
}
