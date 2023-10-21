<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Awobaz\Compoships\Compoships;

class OdooProductPricelist extends Model
{
    use HasFactory, Compoships;

    protected $table = 'odoo_product_pricelist';
    protected $guarded = ['id'];
    protected $appends = [];
    protected $hidden = ['created_at', 'updated_at', 'note'];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope('grouping_type_handling', function (Builder $builder) {
    //         $builder->groupBy('min_quantity')->groupBy('type')->groupBy('product_tmpl_id');
    //     });
    // }

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_tmpl_id', 'product_tmpl_id')->withTrashed();
    }

    public function product_uom(){
        return $this->belongsTo(OdooProductUom::class, ['product_uom_id', 'product_tmpl_id', 'type'], ['product_uom_id', 'product_tmpl_id', 'type']);
    }
}
