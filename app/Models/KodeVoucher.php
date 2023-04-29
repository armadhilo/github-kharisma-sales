<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Blameable;

class KodeVoucher extends Model
{
    use HasFactory, Blameable, SoftDeletes;

    protected $table = 'kode_voucher';
    protected $guarded = ['id'];
    protected $hidden = ['user_id', 'status', 'is_claim'];
    protected $appends = ['deskripsi_bonus', 'user_desc', 'masa_aktif'];

    public function scopeActive($query){
        return $query->where(function($q){
            $q->where('user_id', auth('api')->user()->id)->orWhereNull('user_id');
        })->where('tanggal_mulai','<=', date('Y-m-d'))->where('tanggal_selesai','>=', date('Y-m-d'))->where(['status' => '1', 'is_claim' => '0']);
    }

    public function scopeMustClaim($query){
        return $query->where(function($q){
            $q->where('user_id', auth('api')->user()->id)->orWhereNull('user_id');
        })->where('tanggal_mulai','<=', date('Y-m-d'))->where('tanggal_selesai','>=', date('Y-m-d'))->where(['status' => '1', 'is_claim' => '1']);
    }

    public function getDeskripsiBonusAttribute(){
        return 'Cashback ' . $this->attributes['persen_presentase_potongan'] . '% maksimal potongan ' . number_format($this->attributes['persen_maksimal_potongan'],0,',','.');
    }

    public function getUserDescAttribute(){
        return $this->user->nama ?? '';
    }

    public function getMasaAktifAttribute(){
        return $this->attributes['tanggal_selesai'] != null ? 'Berakhir dalam : ' . \Carbon\Carbon::createFromTimeStamp(strtotime($this->attributes['tanggal_selesai']))->diffInDays(false, now()) . ' hari' : 'Tidak ada expired';
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function kode_voucher_user(){
        return $this->hasMany(KodeVoucherUser::class);
    }
}
