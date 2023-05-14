<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooProduct extends Model
{
    use HasFactory;

    protected $table = 'odoo_product';
    protected $guarded = ['id'];
    protected $appends = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function pricelist(){
        return $this->hasMany(OdooPricelist::class, 'product_tmpl_id', 'product_tmpl_id');
    }

    public function product_tag(){
        return $this->belongsTo(OdooProductTag::class, 'product_tag_id', 'product_tag_id');
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }
}
