<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// class nya bernama Kategori yang diturunkan dr model
class Kategori extends Model
{
    use HasFactory;

    //setting property $table untuk menentukan tabel yg akan diakses
    protected $table = 'kategori';

    //scope akses dibatasi hanya satu local class menggunakan protected, jd yg dpt diisi hanya deskripsi dan kategori
    //$fillable property bertipe array yang berisi field-field tabel
    protected $fillable = ['deskripsi','kategori'];

    public static function getKategoriAll(){
        return DB::table('kategori')
            ->select('kategori.id','deskripsi',DB::raw('ketKategorik(kategori) as ketkategorik'));
    }

    // public static function infoKategori(){
    // return DB::table('kategori')
    //         ->select('kategori.id','deskripsi',DB::raw('infoKategori(kategori) as infokategori')) -> get();
    // }

    public function ketKategorik()
    {
        switch ($this->kategori) {
            case 'M':
                return 'Barang Modal';
            case 'A':
                return 'Alat';
            case 'BHP':
                return 'Bahan Habis Pakai';
            case 'BTHP':
                return 'Bahan Tidak Habis Pakai';
            default:
                return 'Unknown';
        }
    }
    
}