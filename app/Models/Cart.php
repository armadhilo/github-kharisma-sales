<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Cart extends Model
{
    use HasFactory,Compoships;

    protected $table = 'cart';
    protected $guarded = ['id'];
    protected $appends = ['sisa_stock'];

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_id', 'product_id')->withTrashed();
    }

    public function product_uom(){
        return $this->hasMany(OdooProductUom::class, ['product_tmpl_id', 'product_uom_id'], ['product_tmpl_id', 'product_uom_id']);
    }

    public function user(){
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function scopeActive($query){
        $query->where(['user_id' => auth('api')->user()->pelanggan_id, 'status' => 0])->has('product');
    }

    public function scopeBuyNow($query){
        $query->where(['user_id' => auth('api')->user()->pelanggan_id, 'status' => 0, 'is_buy_now' => 1])->has('product');
    }

    public function getSisaStockAttribute(){
        return $this->product->stock - ($this->satuan_stock * $this->quantity);
    }
}
