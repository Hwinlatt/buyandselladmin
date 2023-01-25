<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\region_city;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{

    public function index()
    {
        $regions = region_city::all();
        return view('city.index', compact('regions'));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = $this->validation($request, null);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        $region = region_city::where('region', $request->region)->where('city', $request->city)->count();
        if ($region > 0) {
            return response()->json(['error' => ['Already exist!']], 200);
        } else {
            $region = region_city::create([
                'region' => $request->region,
                'city' => $request->city,
            ]);
            return response()->json(['success' => 'Region Added.'], 200);
        }

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validation($request, $id);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        $region = region_city::find($id);
        $region->update([
            'region' => $request->region,
            'city' => $request->city,
        ]);
        return response()->json(['success' => 'Region Updated.'], 200);
    }

    public function destroy($id)
    {
        region_city::find($id)->delete();
        return response()->json(['success'=>'Deleted Success!'], 200);
    }


    private function validation($request, $id)
    {
        $validator = Validator::make($request->all(), [
            'region' => 'required',
            'city' => 'required|unique:region_cities,city,' . $id,
        ]);
        return $validator;
    }
}
