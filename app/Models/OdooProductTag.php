<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooProductTag extends Model
{
    use HasFactory;

    protected $table = 'odoo_product_tag';
    protected $guarded = ['id'];
    protected $appends = [];
}
