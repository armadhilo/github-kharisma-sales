<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooProduct extends Model
{
    use HasFactory;

    protected $table = 'odoo_product';
    protected $guarded = ['id'];
    protected $appends = [];
}
