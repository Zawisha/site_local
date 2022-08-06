<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastUsers extends Model
{
    use HasFactory;
    protected $table = 'last_users';
    protected $fillable = ['id','channel', 'technology', 'text'];
}
