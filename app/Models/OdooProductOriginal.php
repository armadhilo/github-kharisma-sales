<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OdooProductOriginal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'odoo_product';
    protected $guarded = ['id'];
    protected $appends = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function product_uom(){
        return $this->hasMany(OdooProductUom::class, 'product_tmpl_id', 'product_tmpl_id');
    }

    public function product_tag(){
        return $this->belongsTo(OdooProductTag::class, 'product_tag_id', 'product_tag_id');
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }
}
