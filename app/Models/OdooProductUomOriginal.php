<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Awobaz\Compoships\Compoships;

class OdooProductUomOriginal extends Model
{
    use HasFactory, Compoships;

    protected $table = 'odoo_product_uom';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_tmpl_id', 'product_tmpl_id')->withTrashed();
    }

    public function product_pricelist(){
        return $this->hasMany(OdooProductPricelist::class, ['product_uom_id', 'product_tmpl_id'], ['product_uom_id', 'product_tmpl_id'])->orderBy('min_quantity', 'ASC')->orderBy('price', 'ASC');
    }
}
