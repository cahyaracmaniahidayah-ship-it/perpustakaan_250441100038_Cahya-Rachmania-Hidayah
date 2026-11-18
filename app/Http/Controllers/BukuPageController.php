<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Halaman web buku: daftar, detail, dan pencarian. */
class BukuPageController extends Controller
{
    public function index()
    {
        $bukus = Buku::with('kategori')->orderBy('id')->get();

        return view('buku.index', compact('bukus'));
    }

    public function show($id)
    {
        $model = Buku::with('kategori')->find($id);

        $buku = $model ? [
            'id' => $model->id,
            'judul' => $model->judul,
            'penulis' => $model->penulis,
            'tahun_terbit' => $model->tahun_terbit,
            'kategori' => $model->kategori->nama ?? '-',
        ] : null;

        $sudahAktif = $model
            ? Peminjaman::where('user_id', auth()->id())
                ->where('buku_id', $model->id)
                ->whereIn('status', [Peminjaman::STATUS_DIAJUKAN, Peminjaman::STATUS_DIPINJAM])
                ->exists()
            : false;

        return response()->view('buku.show', compact('buku', 'id', 'sudahAktif'), $model ? 200 : 404);
    }

    public function cari(Request $request)
    {
        $judul = (string) $request->query('judul', '');
        $hasil = DB::select('SELECT * FROM bukus WHERE judul = ?', [$judul]);

        return response()->json($hasil);
    }
}