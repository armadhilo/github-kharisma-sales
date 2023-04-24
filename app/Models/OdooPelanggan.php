<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooPelanggan extends Model
{
    use HasFactory;

    protected $table = 'odoo_pelanggan';
    protected $guarded = ['id'];
    protected $appends = [];
}
