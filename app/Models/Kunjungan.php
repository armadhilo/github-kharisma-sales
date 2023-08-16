<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class Kunjungan extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'kunjungan';
    protected $guarded = ['id'];
    protected $appends = [];

    public function sales(){
        return $this->belongsTo(User::class, 'sales_id')->withTrashed();
    }

    public function pelanggan(){
        return $this->belongsTo(User::class, 'pelanggan_id')->withTrashed();
    }
}
