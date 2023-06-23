<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';
    protected $guarded = ['id'];
    protected $appends = [];

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_id', 'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query){
        $query->where(['user_id' => auth('api')->user()->id, 'status' => 0])->whereHas('product');
    }
}
