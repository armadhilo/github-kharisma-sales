<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['type_pelanggan'];
    protected $append = ['sales'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'keylog',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getTypeAttribute(){
        if($this->attributes['type'] == '1'){
            return 'sales';
        }elseif($this->attributes['type'] == '2'){
            return 'pelanggan';
        }else{
            return 'superadmin';
        }
    }

    public function setTypeAttribute($value){
        if($value == 'sales'){
            $this->attributes['type'] = 1;
        }elseif($value == 'pelanggan'){
            $this->attributes['type'] = 2;
        }else{
            $this->attributes['type'] = 0;
        }
    }

    // Accessor to automatically decode the JSON when accessing the attribute
    public function getAccessMenuAttribute($value)
    {
        return json_decode($value, true);
    }

    // Mutator to automatically encode the array as JSON before storing it in the database
    public function setAccessMenuAttribute($value)
    {
        $this->attributes['access_menu'] = json_encode($value);
    }

    public function AauthAcessToken(){
        return $this->hasMany(OauthAccessToken::class);
    }

    public function getSalesAttribute(){
        return User::where('sales_id', session('sales_id'))->first();
    }

    public function scopePelangganActive($query){
        return $query->where(['type' => 2])->has('alamat_pengiriman')->whereNotNull('keylog');
    }

    public function scopeSalesActive($query){
        return $query->where(['type' => 1])->whereNotNull('email');
    }

    public function sales_log(){
        return $this->hasMany(SalesLog::class, 'sales_id', 'pelanggan_id');
    }

    public function alamat_pengiriman(){
        return $this->hasOne(AlamatPengiriman::class, 'user_id', 'pelanggan_id');
    }

    public function sales_pelanggan(){
        return $this->hasMany(SalesPelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }
}
