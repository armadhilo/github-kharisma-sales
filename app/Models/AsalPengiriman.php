<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Blameable;

class AsalPengiriman extends Model
{
    use HasFactory, Blameable;

    protected $table = 'asal_pengiriman';
    protected $guarded = ['id'];
}
