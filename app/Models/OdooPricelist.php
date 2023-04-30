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
    protected $hidden = ['created_at', 'updated_at', 'id','product_tmpl_id'];

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_tmpl_id', 'product_tmpl_id');
    }
}
