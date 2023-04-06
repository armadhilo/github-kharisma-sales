<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
    protected $append = ['sales'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'keylog'
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

    public function AauthAcessToken(){
        return $this->hasMany(OauthAccessToken::class);
    }

    public function getSalesAttribute(){
        return User::where('sales_id', session('sales_id'))->first();
    }
}
