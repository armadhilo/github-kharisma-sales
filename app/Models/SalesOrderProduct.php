<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class SalesOrderProduct extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'sales_order_product';
    protected $guarded = ['id'];
    protected $appends = ['total_harga_produk'];

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_id', 'product_id');
    }

    public function getTotalHargaProdukAttribute(){
        return $this->attributes['harga'] * $this->attributes['quantity'];
    }
}
