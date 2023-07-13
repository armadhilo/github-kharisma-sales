<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';
    protected $guarded = ['id'];
    protected $appends = ['notification_title', 'notification_body', 'custom_datetime'];

    public function scopeActive($query){
        $query->select('id','notification_type', 'is_new','judul','pengumuman','sales_order_id','created_at')->where(function($where){
            $where->where('notification_type', '!=', 'pengumuman')->where('user_id', auth('api')->user()->id)->where('status', 1);
        })->orWhere(function($where2){
            $where2->where('notification_type', 'pengumuman')->where('status', 1);
        })->orderBy('is_new', 'desc')->orderBy('created_at', 'desc');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function sales_order(){
        return $this->belongsTo(SalesOrder::class);
    }

    public function getCustomDatetimeAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
    }

    public function getNotificationTitleAttribute(){
        switch ($this->attributes['notification_type']) {
            case "draft":
                return "Transaksi " .$this->sales_order->no_invoice. " sedang menunggu untuk diproses";
                break;
            case "proses_order":
                return "Transaksi " .$this->sales_order->no_invoice. " sedang kami proses";
                break;
            case "transaksi_cancel":
                return "Transaksi " .$this->sales_order->no_invoice. " telah dibatalkan";
                break;
            case "transaksi_selesai":
                return "Transaksi " .$this->sales_order->no_invoice. " telah selesai";
                break;
            case "new_user":
                return "Hallo New Juragan, Selamat berbelanja di Kharisma Stationary";
                break;
            default:
                return $this->attributes['judul'];
        }
    }

    public function getNotificationBodyAttribute(){
        switch ($this->attributes['notification_type']) {
            case "draft":
                return "Tunggu pesananmu sedang menunggu acc admin :) Terimakasih telah berbelanja di Kharisma Stationary";
                break;
            case "proses_order":
                return "Tunggu pesananmu sedang kami proses secepat mungkin :) Terimakasih telah berbelanja di Kharisma Stationary";
                break;
            case "transaksi_cancel":
                return "Oops! transaksimu dengan Invoice " .$this->transaksi->no_invoice. " telah berhasil dibatalkan";
                break;
            case "transaksi_selesai":
                return "Yeay transaksi dengan Invoice " .$this->transaksi->no_invoice. " telah selesai terimakasih telah berbelanja di Kharisma Stationary";
                break;
            case "new_user":
                return "Selamat Datang Juragan " . auth('api')->user()->nama . " terimakasih telah bergabung bersama kami dan happy shopping di Kharisma Stationary :)";
                break;
            default:
                return $this->attributes['pengumuman'];
        }
    }
}
