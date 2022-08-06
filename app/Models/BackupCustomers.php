<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupCustomers extends Model
{
    use HasFactory;
    protected $table = 'backup_customers';
    protected $fillable = ['id','text'];
}
