<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OdooProductOriginal extends Model
{
    use HasFactory;

    protected $table = 'odoo_product';
    protected $guarded = ['id'];
    protected $appends = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function pricelist(){
        return $this->hasMany(OdooPricelist::class, 'product_tmpl_id', 'product_tmpl_id');
    }

    public function pricelist_b2b()
    {
        return $this->hasMany(OdooPricelist::class, 'product_tmpl_id', 'product_tmpl_id')->where('type', 'b2b');
    }

    public function pricelist_b2c()
    {
        return $this->hasMany(OdooPricelist::class, 'product_tmpl_id', 'product_tmpl_id')->where('type', 'b2c');
    }

    public function product_tag(){
        return $this->belongsTo(OdooProductTag::class, 'product_tag_id', 'product_tag_id');
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }
}
