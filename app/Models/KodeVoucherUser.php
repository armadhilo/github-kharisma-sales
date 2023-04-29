<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Blameable;

class KodeVoucherUser extends Model
{
    use HasFactory, Blameable, SoftDeletes;

    protected $table = 'kode_voucher_users';
    protected $guarded = ['id'];

    public function scopeActive($query){
        $query->where(['user_id' => auth('api')->user()->id, 'status' => 1]);
    }

    public function user(){
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function kode_voucher(){
        return $this->belongsTo(KodeVoucher::class)->withTrashed();
    }
}
