<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
	public function index(Request $request)
    {
        //QUERY MENGGUNAKAN MODEL CATEGORY, DIMANA KETIKA PARAMETER Q TIDAK KOSONG
        $categories = Category::when($request->q, function($categories) use($request) {
            //MAKA AKAN DILAKUKAN FILTER BERDASARKAN NAME
            $categories->where('name', 'LIKE', '%' . $request->q . '%');
        })->orderBy('created_at', 'DESC')->paginate(10); //DAN DIORDER BERDASARKAN DATA TERBARU
        return response()->json(['status' => 'success', 'data' => $categories]);
    }
    
    public function store(Request $request)
    {
        //VALIDASI DATA YANG DITERIMA
        $this->validate($request, [
            'name' => 'required|string|unique:categories,name', //NAME BERSIFAT UNIK
            'description' => 'nullable|string|max:150'
        ]);

        //SIMPAN DATA KE TABLE CATEGORIES MENGGUNAKAN MASS ASSIGNMENT ELOQUENT
        Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return response()->json(['status' => 'success']);
    }
    
    public function edit($id)
    {
        $category = Category::find($id); //MENGAMBIL DATA BERDASARKAN ID
        return response()->json(['status' => 'success', 'data' => $category]); //DAN MENGIRIMKAN RESPONSE BERUPA DATA YANG DIAMBIL DARI DATABASE
    }

    public function update(Request $request, $id)
    {
        //VALIDASI DATA 
        $this->validate($request, [
            //DIMANA NAME MASIH BERSIFAT UNIK TAPI DIKECUALIKAN UNTUK ID YANG SEDANG DIEDIT
            'name' => 'required|string|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:150'
        ]);

        $category = Category::find($id); //AMBIL DATA BERDASARKAN ID
        //DAN PERBAHARUI DATA
        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return response()->json(['status' => 'success']);
    }
    
    public function destroy($id)
    {
        $category = Category::find($id); //MENGAMBIL DATA BERDASARKAN ID
        $category->delete(); //MENGHAPUS DATA
        return response()->json(['status' => 'success']);
    }
}