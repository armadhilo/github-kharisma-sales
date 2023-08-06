<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;
use Awobaz\Compoships\Compoships;

class SalesOrderProduct extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity, Compoships;

    protected static $logAttributes = ["*"];

    protected $table = 'sales_order_product';
    protected $guarded = ['id'];
    protected $appends = ['total_harga_produk'];

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_id', 'product_id')->withTrashed();
    }

    public function product_original(){
        return $this->belongsTo(OdooProductOriginal::class, 'product_id', 'product_id');
    }

    public function product_uom(){
        return $this->belongsTo(OdooProductUom::class, ['product_uom_id', 'product_tmpl_id'], ['product_uom_id', 'product_tmpl_id']);
    }

    public function getTotalHargaProdukAttribute(){
        return $this->attributes['harga'] * $this->attributes['quantity'];
    }
}
