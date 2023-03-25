<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class MasterMenu extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'master_menu';
    protected $guarded = ['id'];
    protected $appends = [];

    public function scopeActive($query){
        $query->where('status', 1);
    }
}
