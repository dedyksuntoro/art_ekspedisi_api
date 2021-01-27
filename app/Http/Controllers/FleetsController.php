<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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

    public function store(Request $request)
    {
        //MEMBUAT VALIDASI DATA YANG DITERIMA
        $this->validate($request, [
            'plate_number' => 'required|string|unique:fleets,plate_number', //HARUS BERSIFAT UNIK
            'type' => 'required',
            'photo' => 'required|image|mimes:jpg,jpeg,png' //FILE GAMBAR YG DIIZINKAN HANYA JPG,JPEG DAN PNG
        ]);

        $user = $request->user(); //MENGAMBIL USER YANG SEDANG LOGIN
        $file = $request->file('photo'); //MENGAMBIL FILE YANG DIUPLOAD
        //MEMBUAT NAMA BARU UNTUK FILE YANG AKAN DISIMPAN
        $filename = $request->plate_number . '-' . time() . '.' . $file->getClientOriginalExtension();
        //MEMINDAHKAN FILE YANG DITERIMA KE DALAM FOLDER PUBLIC/FLEETS DENGAN MENGGUNAKAN NAMA BARU YANG SUDAH DIBUAT
        $file->move('fleets', $filename);

        //SIMPAN INFORMASI DATA ARMADANYA KE TABLE FLEETS MELALUI MODEL FLEET
        Fleet::create([
            'plate_number' => $request->plate_number,
            'type' => $request->type,
            'photo' => $filename, //GUNAKAN NAMA FILE YANG SUDAH DIBUAT UNTUK MENGENALI GAMBAR
            'user_id' => $user->id
        ]);
        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $fleet = Fleet::find($id); //MENGAMBIL DATA ARMADA BERDASARKAN ID
        return response()->json(['status' => 'success', 'data' => $fleet]); //KIRIMKAN RESPONSE DATA YANG DIMINTA
    }

    //FUNGSI UNTUK MEMPERBAHARUI DATA
    public function update(Request $request, $id)
    {
        //BUAT VALIDASI DATA YANG AKAN DIUPDATE
        $this->validate($request, [
            'plate_number' => 'required|string|unique:fleets,plate_number,' . $id, //UNIK KECUALI DATA YAGN SEDANG DIEDIT
            'type' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png' //GAMBAR BOLEH KOSONG
        ]);

        $fleet = Fleet::find($id); //AMBIL DATA BERDASARKAN ID
        $filename = $fleet->photo; //SIMPAN NAMA FILE GAMBAR YANG SEBELUMNYA

        //JIKA FILE GAMBARNYA INGIN DIPERBAHARUI
        if ($request->hasFile('photo')) {
            //MAKA LAKUKAN HAL SAMA SEPERTI SEBELUMNYA UNTUK MENYIMPAN FILE GAMBAR
            $file = $request->file('photo');
            $filename = $request->plate_number . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move('fleets', $filename);

            File::delete(base_path('public/fleets/' . $fleet->photo)); //HAPUS GAMBAR YANG LAMA
        }
        //DAN PERBAHARUI DATANYA DI DATABASE
        $fleet->update([
            'plate_number' => $request->plate_number,
            'type' => $request->type,
            'photo' => $filename
        ]);
        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $fleet = Fleet::find($id); //AMBIL DATA BERDASARKAN ID
        File::delete(base_path('public/fleets/' . $fleet->photo)); //HAPUS FILE GAMBAR
        $fleet->delete(); //HAPUS DATA DARI DATABASE
        return response()->json(['status' => 'success']);
    }

}
