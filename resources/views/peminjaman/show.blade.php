@extends('layouts.main')

@section('title', 'Detail Peminjaman')

@section('content')
    <div class="detail">
        <h2>Detail Peminjaman #{{ $peminjaman->id }}</h2>

        <table>
            <tr><th>Peminjam</th><td>{{ $peminjaman->user->name ?? $peminjaman->anggota->nama ?? '-' }}</td></tr>
            <tr><th>Email</th><td>{{ $peminjaman->user->email ?? '-' }}</td></tr>
            <tr><th>Buku</th><td>{{ $peminjaman->buku->judul ?? '-' }}</td></tr>
            <tr><th>Tanggal Pinjam</th><td>{{ $peminjaman->tanggal_pinjam?->format('d-m-Y') }}</td></tr>
            <tr><th>Tanggal Kembali</th><td>{{ $peminjaman->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge-st st-{{ $peminjaman->status }}">{{ ucfirst($peminjaman->status) }}</span></td></tr>
        </table>

        <a href="{{ route('home') }}" class="tombol tombol-abu">&larr; Kembali</a>
    </div>
@endsection