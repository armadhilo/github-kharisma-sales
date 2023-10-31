<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;
use \Carbon\Carbon;

class SalesOrder extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'sales_order';
    protected $guarded = ['id'];
    protected $appends = ['color', 'created_at_desc', 'status_desc'];

    public function getColorAttribute(){
        if($this->attributes['status'] == 'draft') return 'warning';
        if($this->attributes['status'] == 'sale') return 'info';
        if($this->attributes['status'] == 'done') return 'success';
        if($this->attributes['status'] == 'cancel') return 'danger';
    }

    public function getStatusDescAttribute(){
        if($this->attributes['status'] == 'draft') return 'Konfirmasi Admin';
        if($this->attributes['status'] == 'sale') return 'Diproses';
        if($this->attributes['status'] == 'done') return 'Selesai';
        if($this->attributes['status'] == 'cancel') return 'Gagal';
    }

    public function getCreatedAtDescAttribute(){
        return Carbon::parse($this->attributes['created_at'])->isoFormat('D MMMM Y');
    }

    public function sales_order_product(){
        return $this->hasMany(SalesOrderProduct::class);
    }

    public function scopeActive($query){
        return $query->where('user_id', auth('api')->user()->pelanggan_id)->has('sales_order_product');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'pelanggan_id');
    }

    public function sales(){
        return $this->belongsTo(User::class, 'sales_id', 'pelanggan_id');
    }
}
