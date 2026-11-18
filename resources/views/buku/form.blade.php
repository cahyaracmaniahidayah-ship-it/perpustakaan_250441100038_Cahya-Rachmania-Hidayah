@extends('layouts.main')

@section('title', $buku->exists ? 'Ubah Buku' : 'Tambah Buku')

@section('content')
    <h2 class="page-title">{{ $buku->exists ? 'Ubah Buku' : 'Tambah Buku' }}</h2>

    <div class="form-container">
        <form method="POST" action="{{ $buku->exists ? route('buku.update', $buku->id) : route('buku.store') }}">
            @csrf
            @if ($buku->exists)
                @method('PUT')
            @endif

            <div class="form-grup">
                <label for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id" required>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}" @selected(old('kategori_id', $buku->kategori_id) == $k->id)>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-grup">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $buku->judul) }}" required>
            </div>

            <div class="form-grup">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required>
            </div>

            <div class="form-grup">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" min="1900" max="2026"
                       value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('buku.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection