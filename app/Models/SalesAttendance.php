<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class SalesAttendance extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'sales_attendance';
    protected $guarded = ['id'];
    protected $appends = ['note'];

    public function getNoteAttribute(){
        return !$this->attributes['date_end'] ? 'CHECK IN' : 'CHECK OUT';
    }

    public function sales(){
        return $this->belongsTo(User::class, 'sales_id', 'pelanggan_id')->withTrashed();
    }
}
