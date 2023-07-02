<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\ApiFormatter;
use App\Models\Notification;
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notification = Notification::active();

        if($request->has('limit')){
            $notification->limit($request->limit);
        }

        if($request->has('type') && $request->type == 'count'){
            $message = 'Data jumlah notification berhasil ditampilkan';
            $notification = $notification->count();
        }else{
            $message = 'Data notification berhasil ditampilkan';
            $notification = $notification->get();
        }

        return ApiFormatter::success(200, $message, $notification);
    }

    public function mark_one_read($id){
        Notification::where(['id' => $id])->update(['is_new' => 0]);

        $message = 'Notification berhasil dibaca';
        return ApiFormatter::success(200, $message);
    }

    public function mark_all_read(){
        Notification::active()->where(['is_new' => 1])->update(['is_new' => 0]);

        $message = 'Semua notification berhasil dibaca';
        return ApiFormatter::success(200, $message);
    }
}
