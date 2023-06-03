<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Models\OdooProduct;
use App\Models\OdooProductTag;

class CategoryController extends Controller
{
    public function index()
    {
        $category = OdooProductTag::all();
        $message = ' Data kategori berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $category);
    }
}
