<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OdooProduct extends Model
{
    use HasFactory;

    protected $table = 'odoo_product';
    protected $guarded = ['id'];
    protected $appends = ['is_wishlist'];
    protected $hidden = ['created_at', 'updated_at'];


    protected static function boot()
    {
        parent::boot();

        if(auth('api')->check()){

            //pricelist user -> b2c and b2b
            static::addGlobalScope('pricelist_user', function (Builder $builder) {
                $builder->whereHas('pricelist', function($query){
                    $query->where('type', 'b2b');
                })->whereHas('pricelist', function($query){
                    $query->where('type', 'b2c');
                })->where('stock', '>', '0');
            });

        }else{

            //pricelist guest -> b2c
            static::addGlobalScope('pricelist_user', function (Builder $builder) {
                $builder->whereHas('pricelist', function($query){
                    $query->where('type','b2c');
                })->where('stock', '>', '0');
            });

        }
    }

    public function pricelist(){
        return $this->hasMany(OdooProductPricelist::class, 'product_tmpl_id', 'product_tmpl_id');
    }

    public function product_tag(){
        return $this->belongsTo(OdooProductTag::class, 'product_tag_id', 'product_tag_id');
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id')->where('user_id', auth('api')->user()->id);
    }

    public function getIsWishlistAttribute(){
        $wishlist = Wishlist::where('product_id', $this->attributes['product_id'])->first();
        return !empty($wishlist) ? 1 : 0;
    }
}
