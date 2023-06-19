<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OdooPricelist extends Model
{
    use HasFactory;

    protected $table = 'odoo_pricelist';
    protected $guarded = ['id'];
    protected $appends = [];
    protected $hidden = ['created_at', 'updated_at', 'id','product_tmpl_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('grouping_type_handling', function (Builder $builder) {
            $builder->groupBy('min_quantity')->groupBy('type')->groupBy('product_tmpl_id');
        });
    }

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_tmpl_id', 'product_tmpl_id');
    }
}
