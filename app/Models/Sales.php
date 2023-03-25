<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class Sales extends Model
{
    use HasFactory, Blameable, SoftDeletes, LogsActivity;

    protected static $logAttributes = ["*"];

    protected $table = 'sales';
    protected $guarded = ['id'];
    protected $appends = [];
}
