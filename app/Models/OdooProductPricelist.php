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

    protected static function boot()
    {
        parent::boot();

        $type_pelanggan = 'RETAIL (IDR)';
        if(isset(auth('api')->user()->type_pelanggan)) $type_pelanggan = auth('api')->user()->type_pelanggan;
        elseif(isset(auth()->user()->type_pelanggan)) $type_pelanggan = auth()->user()->type_pelanggan;

        //kalo login mengikuti tipe pelanggan
        static::addGlobalScope('type_pelanggan', function (Builder $builder) use ($type_pelanggan) {
            $builder->where('type', $type_pelanggan);
        });
    }

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_tmpl_id', 'product_tmpl_id')->withTrashed();
    }

    public function product_uom(){
        return $this->belongsTo(OdooProductUom::class, ['product_uom_id', 'product_tmpl_id', 'type'], ['product_uom_id', 'product_tmpl_id', 'type']);
    }
}
