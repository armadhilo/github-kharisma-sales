<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class SalesOrder extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'sales_order';
    protected $guarded = ['id'];

    public function sales_order_product(){
        return $this->hasMany(SalesOrderProduct::class);
    }

    public function scopeActive($query){
        return $query->where('user_id', auth('api')->user()->id)->has('sales_order_product');
    }
}
