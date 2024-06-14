<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\Storage;
use DB;

//use Illuminate\Pagination\Paginator;

class BarangController extends Controller
{
    public function index(Request $request)
    {

        //mengurutkan dari data inputan terbaru dari atas ke bawah
        // $rsetBarang = Barang::with('kategori')->latest()->paginate(1);

        // return view('view_barang.index', compact('rsetBarang'))
        //     ->with('i', (request()->input('page', 1) - 1) * 10);

        //mengurutkan data dari data inputan terakhir dari bawah ke atas
        // Paginator::useBootstrap();
        $keyword = $request->input('keyword');

        // Query untuk mencari barang berdasarkan keyword
        $rsetBarang = Barang::where('merk', 'LIKE', "%$keyword%")
            ->orWhere('seri', 'LIKE', "%$keyword%")
            ->orWhere('spesifikasi', 'LIKE', "%$keyword%")
            ->orWhere('stok', 'LIKE', "%$keyword%")
            ->orWhereHas('kategori', function ($query) use ($keyword) {
                $query->where('deskripsi', 'LIKE', "%$keyword%");
            })
            ->paginate(10);
            
            return view('view_barang.index', compact('rsetBarang'));

    }

    public function create()
    {
        $akategori = Kategori::all();
        return view('view_barang.create',compact('akategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'merk'          => 'required|string|max:50',
            'seri'          =>'required|string|max:50',
            'spesifikasi'   => 'nullable|string',
            'stok'          =>'required',
            'kategori_id'   => 'required|exists:kategori,id'

        ]);

        Barang::create([
            'merk'             => $request->merk,
            'seri'             => $request->seri,
            'spesifikasi'      => $request->spesifikasi,
            'stok'             => $request->stok,
            'kategori_id'      => $request->kategori_id,
        ]);

        //menampilakan pesan validasi bahwa saat berhasil menambah barang baru, maka akan menampilkan pesan data tersimpan
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id)
    {
        $rsetBarang = Barang::find($id);

        return view('view_barang.show', compact('rsetBarang'));
    }

    public function edit(string $id)
    {
    $akategori = Kategori::all();
    $rsetBarang = Barang::find($id);
    $selectedKategori = Kategori::find($rsetBarang->kategori_id);

    return view('view_barang.edit', compact('rsetBarang', 'akategori', 'selectedKategori'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'merk'        => 'required',
            'seri'        => 'required',
            'spesifikasi' => 'required',
            'stok'        => 'required',
            'kategori_id' => 'required',
        ]);

        $rsetBarang = Barang::find($id);

            $rsetBarang->update([
                'merk'          => $request->merk,
                'seri'          => $request->seri,
                'spesifikasi'   => $request->spesifikasi,
                'stok'          => $request->stok,
                'kategori_id'   => $request->kategori_id,
            ]);

        //menampilkan pesan validasi bahwa saat berhasil mengupdate barang baru, maka akan menampilkan pesan data berhasil disimpan
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Diubah!']);
    }
     /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rsetBarang = Barang::find($id);
    
        // menampilkan pesan validasi untuk mengecek data barang masih berelasi dengan barangkeluar atau tidak, jika iya maka barang tidak bisa dihapus
        $relatedBarangKeluar = BarangKeluar::where('barang_id', $id)->exists();
        $relatedBarangMasuk = BarangMasuk::where('barang_id', $id)->exists();
        $relatedBarang      = Barang::where('id', $id)->where('stok', '>', 0 )->exists();

        if ($relatedBarangKeluar || $relatedBarangMasuk || $relatedBarang ) {
            return redirect()->route('barang.index')->with(['gagal' => 'Data Gagal Dihapus! Barang memiliki stok lebih dari 0 dan masih digunakan dalam tabel Barang Masuk dan Keluar!']);
        }

        //menampilkan pesan validasi jika data barang tidak memiliki relasi pada barang keluar dan masuk, maka barang akan berhasil dihapus
        $rsetBarang->delete();
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}