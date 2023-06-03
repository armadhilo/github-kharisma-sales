<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Blameable;

class Config extends Model
{
    use HasFactory;

    protected $table = 'config';
    protected $guarded = ['id'];
    protected $appends = [];
}
