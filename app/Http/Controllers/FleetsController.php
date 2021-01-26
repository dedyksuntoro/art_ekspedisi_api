<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Fleet;

class FleetsController extends Controller
{
    public function index(Request $request)
    {
        //QUERY UNTUK MENGAMBIL DATA ARMADA DENGAN MENGURUTKAN BERDASARKAN CREATED_AT
        //DAN JIKA Q TIDAK KOSONG
        $fleets = Fleet::orderBy('created_at', 'DESC')->when($request->q, function($fleets) {
          //MAKA FUNGSI FILTER BERDASARKAN PLAT NOMOR AKAN DIJALANKAN
            $fleets->where('plate_number', $request->plate_number);
        })->paginate(10); //LOAD 10 DATA PERHALAMAN
        return response()->json(['status' => 'success', 'data' => $fleets]);
    }
    
}