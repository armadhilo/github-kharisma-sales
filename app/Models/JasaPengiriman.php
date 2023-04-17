<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Blameable;

class JasaPengiriman extends Model
{
    use HasFactory, Blameable, SoftDeletes;

    protected $table = 'jasa_pengiriman';
    protected $guarded = ['id'];
    protected $appends = ['status_desc','status_badge'];

    public function scopeActive($query){
        $query->where('status', 1);
    }

    public function getStatusDescAttribute(){
        return $this->attributes['status'] == 1 ? 'aktif' : 'non-aktif';
    }

    public function getStatusBadgeAttribute(){
        return $this->attributes['status'] == 1 ? '<span class="badge badge-success">active</span>' : '<span class="badge badge-danger">non-aktif</span>';
    }
}
