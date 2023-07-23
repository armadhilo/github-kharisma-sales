<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Helpers\ApiFormatter;
use App\Http\Requests\LongLatRequest;
use App\Models\User;

class LongLatController extends Controller
{
    public function update($id, LongLatRequest $request){

        User::where('id', $id)->update([
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        $message = 'Pelanggan location updated Successfully';
        return ApiFormatter::success(200, $message);
    }
}
