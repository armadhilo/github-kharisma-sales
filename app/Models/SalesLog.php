<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesLog extends Model
{
    use HasFactory;

    protected $table = 'sales_log';
    protected $guarded = ['id'];
    protected $appends = [];

    public function sales(){
        return $this->belongsTo(Sales::class);
    }
}
