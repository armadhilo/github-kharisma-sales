<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooPricelist extends Model
{
    use HasFactory;

    protected $table = 'odoo_pricelist';
    protected $guarded = ['id'];
    protected $appends = [];
}
