<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Session;


class KategoriController extends Controller
{
    // scope : public, method : index
    public function index(Request $request)
    {
    /**
    * Display a listing of the resource.
    */

        // $rsetKategori = DB::table('kategori')->select('id','deskripsi',DB::raw('ketKategorik(kategori) as ketkategori'))->paginate(10);
        // return view('view_kategori.index',compact('rsetKategori'))
        //     ->with('i', (request()->input('page', 1) - 1) * 10);

        $keyword = $request->input('keyword');

        // Query untuk mencari kategori berdasarkan keyword
        $query = DB::table('kategori')
            ->select('id', 'deskripsi', DB::raw('ketKategorik(kategori) as ketkategorik'))
            ->orderBy('kategori', 'asc');
    
        if (!empty($keyword)) {
            $query->where('deskripsi', 'LIKE', "%$keyword%")
                  ->orWhereRaw('ketKategorik(kategori) COLLATE utf8mb4_unicode_ci LIKE ?', ["%$keyword%"]);
        }
    
        $rsetKategori = $query->paginate(10);
    
        return view('view_kategori.index', compact('rsetKategori'))
            ->with('i', ($request->input('page', 1) - 1) * 10);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aKategori = array('blank'=>'Pilih Kategori',
                            'M'=>'Barang Modal',
                            'A'=>'Alat',
                            'BHP'=>'Bahan Habis Pakai',
                            'BTHP'=>'Bahan Tidak Habis Pakai'
                            );
        return view('view_kategori.create',compact('aKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
     
        //mengembalikan object yang bernama request pada name'kategori'(name ini dpt dilihat di create.blade.php)
        // return $request->kategori;

        // untuk menghentikan
        //die();

        $request->validate([
            'deskripsi'   => 'required|unique:kategori',
            'kategori'     => 'required | in:M,A,BHP,BTHP',
        ]);


        //create post
        // Kategori::create([
        //     'deskripsi'  => $request->deskripsi,
        //     'kategori' => $request->kategori
        // ]);

        {
            // Validate the request
            $request->validate([
                'deskripsi' => 'required|unique:kategori',
                'kategori'  => 'required|in:M,A,BHP,BTHP',
            ]);
    
            try {
                DB::beginTransaction(); // Start the transaction
    
                // Insert a new category using Eloquent
                Kategori::create([
                    'deskripsi' => $request->deskripsi,
                    'kategori'  => $request->kategori,
                    'status'    => 'pending',
                ]);
    
                DB::commit(); // Commit the changes
    
                // Flash success message to the session
                Session::flash('success', 'Kategori berhasil disimpan!');
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback in case of an exception
                report($e); // Report the exception
    
                // Flash failure message to the session
                Session::flash('gagal', 'Kategori gagal disimpan!');
            }
    
            // Redirect to the index route with a success message
            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetKategori = Kategori::find($id);

        // $rsetKategori = Kategori::select('id','deskripsi','kategori',
        //     \DB::raw('(CASE
        //         WHEN kategori = "M" THEN "Modal"
        //         WHEN kategori = "A" THEN "Alat"
        //         WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
        //         ELSE "Bahan Tidak Habis Pakai"
        //         END) AS ketKategori'))->where('id', '=', $id);

        return view('view_kategori.show', compact('rsetKategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
        {
        // $akategori = Kategori::all();
        $kategori = Kategori::find($id);

        // $selectedKategori = Kategori::find($kategori->kategori_id);
        return view('view_kategori.edit', compact('kategori'));
     
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'deskripsi'              => 'required',
            'kategori'              => 'required',
            // 'spesifikasi'       => 'required',
            // 'stok'              => 'required',
            // 'kategori_id'       => 'required',
        ]);

        $kategori = Kategori::find($id);
            $kategori->update([
                'deskripsi'              => $request->deskripsi,
                'kategori'              => $request->kategori,
                // 'spesifikasi'       => $request->spesifikasi,
                // 'stok'              => $request->stok,
                // 'kategori_id'       => $request->kategori_id
            ]);


        //menampilkan pesan validasi saat mengupdate kategori dan kolom deskripsi derta kategori telah diisi semua, maka data berhasil diubah
        return redirect()->route('kategori.index')->with(['success' => 'Data Kategori Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)

    {
        // mengecek kategori_id apakah ada di barang.kategori_id yaitu jika data kategori masih memiliki relasi dengan barang, barang masuk dan keluar
        if (DB::table('barang')->where('kategori_id', $id)->exists()){

            //Jika iya, maka akan menampilkanan pesan validasi data gagal dihapus
            return redirect()->route('kategori.index')->with(['gagal' => 'Data Gagal Dihapus!']);

        } else {

            $rsetKategori = Kategori::find($id);

            $rsetKategori->delete();

            //jika tidak maka akan menampilkan pesan validasi data berhasil dihapus
            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);

        }
    }

}