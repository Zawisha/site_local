<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupNumber extends Model
{
    use HasFactory;

    protected $table = 'group_number';
    protected $fillable = ['id','group_name'];

}
