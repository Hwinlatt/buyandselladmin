<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\region_city;
use Illuminate\Support\Facades\DB;

class DefaultApiController extends Controller
{

    public function defaultApi()
    {
        $region = region_city::select('region as name',DB::raw('count(*) as total'))->orderBy('region','asc')->groupBy('region')->get();
        $city = region_city::select('city','region')->get();
        $category = Category::all();
        $data = [
            'region'=>$region,
            'city'=>$city,
            'category'=>$category,
        ];
        return response()->json($data, 200);
    }
}
