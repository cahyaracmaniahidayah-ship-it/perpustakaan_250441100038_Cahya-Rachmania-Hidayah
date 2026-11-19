<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\BukuPageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PetugasDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('beranda');
})->name('home');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->middleware('permission:akses dashboard admin')->name('admin.dashboard');
    Route::get('/petugas/dashboard', PetugasDashboardController::class)
        ->middleware('permission:akses dashboard petugas')->name('petugas.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:lihat profil sendiri')->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:ubah profil sendiri')->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('permission:ubah profil sendiri')->name('profile.destroy');

    // ---- Daftar & detail buku (semua role) ----
    Route::middleware('permission:lihat daftar buku')->group(function () {
        Route::get('/buku', [BukuPageController::class, 'index'])->name('buku.index');
        Route::get('/buku/{id}', [BukuPageController::class, 'show'])->whereNumber('id')->name('buku.show');
    });

    // ---- Kelola buku (admin & petugas) ----
    // Soal 3: Gate kelola-buku (menggantikan permission:kelola data buku)
    Route::middleware('can:kelola-buku')->group(function () {
        // Halaman form tambah = halaman daftar yang sama (form ada di atas tabel)
        Route::get('/buku/create', [BukuPageController::class, 'index'])->name('buku.create');

        Route::post('/buku', function (Request $request) {
            $validated = $request->validate([
                'kategori_id' => 'required|integer',
                'judul' => 'required|string|max:255',
                'penulis' => 'required|string|max:255',
                'tahun_terbit' => 'required|integer|min:1901|max:2026',
            ]);

            Buku::create($validated);

            return response()->json([
                'message' => 'Buku berhasil ditambahkan.',
            ], 201);
        })->name('buku.store');

        Route::put('/buku/{buku}', function (Request $request, $buku) {
            $dataBuku = Buku::findOrFail($buku);

            $validated = $request->validate([
                'kategori_id' => 'required|integer',
                'judul' => 'required|string|max:255',
                'penulis' => 'required|string|max:255',
                'tahun_terbit' => 'required|integer|min:1901|max:2026',
            ]);

            $dataBuku->update($validated);

            return response()->json([
                'message' => 'Buku berhasil diubah.',
            ], 200);
        })->whereNumber('buku')->name('buku.update');

        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])
            ->whereNumber('buku')->name('buku.destroy');
    });

    // [A2] route pencarian (rentan/amannya ada di BukuPageController::cari)
    Route::get('/buku-cari', [BukuPageController::class, 'cari'])->name('buku.cari');

    Route::post('/buku/{id}/pinjam', [PeminjamanController::class, 'ajukan'])
        ->middleware('permission:mengajukan peminjaman buku')->whereNumber('id')->name('peminjaman.ajukan');

    Route::get('/riwayat', [RiwayatController::class, 'index'])
        ->middleware('permission:melihat riwayat peminjaman sendiri')->name('riwayat.index');
    Route::get('/riwayat/{user}', [RiwayatController::class, 'show'])->name('riwayat.show');

    Route::middleware('permission:kelola data pengguna')->prefix('admin')->group(function () {
        Route::resource('pengguna', PenggunaController::class)
            ->except('show')->parameters(['pengguna' => 'pengguna']);
    });

    Route::get('/peminjaman', [PeminjamanController::class, 'index'])
        ->middleware('permission:kelola peminjaman')->name('peminjaman.index');
    Route::post('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])
        ->middleware('permission:memproses persetujuan peminjaman')->name('peminjaman.setujui');
    Route::post('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])
        ->middleware('permission:memproses persetujuan peminjaman')->name('peminjaman.tolak');

    // [A4] route detail peminjaman (rentan/amannya ada di PeminjamanController::show)
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])
        ->whereNumber('id')->name('peminjaman.show');

    Route::get('/pengembalian', [PengembalianController::class, 'index'])
        ->middleware('permission:kelola pengembalian')->name('pengembalian.index');
    Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'store'])
        ->middleware('permission:mencatat pengembalian buku')->name('pengembalian.store');

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:lihat laporan perpustakaan')->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])
        ->middleware('permission:lihat laporan perpustakaan')->name('laporan.cetak');

    Route::get('/uji-error', function () {
        throw new RuntimeException('Uji error 250441100038');
    });
});

require __DIR__.'/auth.php';