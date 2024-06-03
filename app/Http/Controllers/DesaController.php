<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Village;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $villages = Village::take(100)->get(); //dibatasi 100 guna mencegah overload data
        return response()->json([
            "success" => true,
            "data" => $villages,
            "message" => "",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:indonesia_villages|size:10', //untuk kode desa wajib diisi 10 digit
            'district_code' => 'required|size:6', //untuk kode diskrit wajib diisi 6 digit
            'name' => 'required|string|max:255',
            'meta' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => $validator->errors(),
            ], 400);
        }

        $data = $request->all();
        $village = Village::create($data);
        return response()->json([
            "success" => true,
            "data" => $village,
            "message" => "Berhasil menambahkan desa baru",
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $village = Village::where('code', $id)->first();
        return response()->json([
            "success" => true,
            "data" => $village,
            "message" => "",
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'district_code' => 'required|exists:indonesia_districts,code|size:6',
            'name' => 'required|string|max:255',
            'meta' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => $validator->errors(),
            ], 400);
        }

        $village = Village::where('code', $id)->first();
        if (!$village) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Data desa tidak ditemukan",
            ], 404);
        }

        $village->district_code = $request->district_code;
        $village->name = $request->name;
        $village->meta = $request->meta;
        $village->save();

        return response()->json([
            "success" => true,
            "data" => $village,
            "message" => "Berhasil mengupdate data desa",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $village = Village::where('code', $id)->first();
        if (!$village) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Data desa tidak ditemukan",
            ], 404);
        }

        $village->delete();
        return response()->json([
            "success" => true,
            "data" => $village,
            "message" => "Berhasil menghapus data desa",
        ]);
    }
}
