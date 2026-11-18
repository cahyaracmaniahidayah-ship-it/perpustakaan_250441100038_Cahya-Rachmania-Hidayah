<?php

use Illuminate\Support\Facades\Route;
// Hapus \Api pada baris di bawah ini agar sesuai dengan letak file Anda
use App\Http\Controllers\BukuController; 
use App\Http\Controllers\Api\RekapController; 

// Membungkus semua route API agar wajib login terlebih dahulu
Route::middleware('auth:sanctum')->group(function () {

    // 1. Hak Akses Umum (Siswa, Petugas, Admin)
    Route::middleware('permission:lihat daftar buku')->group(function () {
        Route::get('buku', [BukuController::class, 'index']);
        Route::get('buku/{buku}', [BukuController::class, 'show']);
    });

    // 2. Hak Akses Khusus Admin & Petugas
    // AMAN (Soal 3): Gate kelola-buku
    // Awal (Soal 1-2): ganti 'can:kelola-buku' menjadi 'permission:kelola data buku'
    Route::middleware('can:kelola-buku')->group(function () {
        Route::post('buku', [BukuController::class, 'store']);
        Route::put('buku/{buku}', [BukuController::class, 'update']);
        Route::delete('buku/{buku}', [BukuController::class, 'destroy']);
    });

    Route::middleware('permission:lihat laporan perpustakaan')->group(function () {
        Route::get('/rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
        Route::get('/rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
    });

});