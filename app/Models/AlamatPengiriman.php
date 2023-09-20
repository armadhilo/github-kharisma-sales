<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Blameable;

class AlamatPengiriman extends Model
{
    use HasFactory, Blameable, SoftDeletes;

    protected $table = 'alamat_pengiriman';
    protected $guarded = ['id'];
    protected $appends = ['alamat_lengkap'];

    // protected static function boot()
    // {
    //     parent::boot();

    //     if(auth('api')->check()){
    //         static::addGlobalScope('alamat_pengiriman_user_id', function (Builder $builder) {
    //             $builder->where('user_id', '=', auth('api')->user()->id);
    //         });
    //     }

    // }

    public function scopeActive($query){
        $query->where('user_id', auth('api')->user()->pelanggan_id)->orderBy('alamat_utama','DESC');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'pelanggan_id')->withTrashed();
    }

    public function getAlamatLengkapAttribute(){
        // return $this->attributes['alamat'] . ", " . $this->attributes['provinsi'] ." - ". $this->attributes['kota']  . " [" . $this->attributes['kode_pos'] . "]";
        return $this->attributes['alamat'];
    }
}
