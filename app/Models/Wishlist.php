<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;

class wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlist';
    protected $guarded = ['id'];
    protected $appends = [];

    protected static function boot()
    {
        parent::boot();

        if(auth('api')->check()){
            static::addGlobalScope('wishlist_user', function (Builder $builder) {
                $builder->where('user_id', auth('api')->user()->id);
            });
        }
    }

    public function product(){
        return $this->belongsTo(OdooProduct::class, 'product_id', 'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
