<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldClients extends Model
{
    use HasFactory;
    protected $table = 'old_clients';
    protected $fillable = ['id','user_id'];
}
