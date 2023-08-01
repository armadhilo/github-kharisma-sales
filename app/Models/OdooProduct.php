<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OdooProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'odoo_product';
    protected $guarded = ['id'];
    protected $appends = ['is_wishlist','price_utama', 'label_utama'];
    protected $hidden = ['created_at', 'updated_at'];


    protected static function boot()
    {
        parent::boot();

        if(auth('api')->check()){

            //kalo login mengikuti tipe pelanggan
            static::addGlobalScope('type_pelanggan', function (Builder $builder) {
                $builder->has('product_uom.product_pricelist')->whereHas('product_uom', function($query){
                    $query->where('type', auth('api')->user()->type_pelanggan);
                })->where('stock', '>', '0');
            });

        }else{

            //kalo tidak login default tipe RETAIL
            static::addGlobalScope('type_pelanggan', function (Builder $builder) {
                $builder->has('product_uom.product_pricelist')->whereHas('product_uom', function($query){
                    $query->where('type','RETAIL (IDR)');
                })->where('stock', '>', '0');
            });

        }
    }

    public function product_uom(){
        return $this->hasMany(OdooProductUom::class, 'product_tmpl_id', 'product_tmpl_id')->orderBy('stock','ASC');
    }

    public function product_tag(){
        return $this->belongsTo(OdooProductTag::class, 'product_tag_id', 'product_tag_id');
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id')->where('user_id', auth('api')->user()->id);
    }

    public function getPriceUtamaAttribute(){
        $odoo_product = OdooProduct::where('id', $this->attributes['id'])->first();
        return $odoo_product->product_uom[0]->product_pricelist[0]->price;
    }

    public function getLabelUtamaAttribute(){
        $odoo_product = OdooProduct::where('id', $this->attributes['id'])->first();
        return $odoo_product->product_uom[0]->label;
    }

    public function getIsWishlistAttribute(){
        $wishlist = Wishlist::where('product_id', $this->attributes['product_id'])->first();
        return !empty($wishlist) ? 1 : 0;
    }
}
