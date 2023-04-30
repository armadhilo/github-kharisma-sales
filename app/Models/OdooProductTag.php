<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooProductTag extends Model
{
    use HasFactory;

    protected $table = 'odoo_product_tag';
    protected $guarded = ['id'];
    protected $appends = [];
    protected $hidden = ['id','product_tag_id','created_at','updated_at'];

    public function product(){
        return $this->hasMany(OdooProduct::class, 'product_tag_id', 'product_tag_id');
    }
}
