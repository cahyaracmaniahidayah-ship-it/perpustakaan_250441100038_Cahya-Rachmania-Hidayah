@extends('layouts.main')

@section('title', 'Daftar Buku')

@section('content')

    <h2 class="page-title">Daftar Buku</h2>

    {{-- KODE BARU: Form Pencarian Buku diletakkan di sini --}}
    <div style="margin-bottom: 20px;">
        <form action="{{ route('buku.cari') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="judul" class="form-control" placeholder="Cari judul buku..." style="width: 300px;">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('buku.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>
    {{-- AKHIR KODE BARU --}}

    @can('kelola-buku')
    <div class="form-container">
        <h3 style="margin-top: 0;">Form Buku</h3>
        <div id="pesan-error" style="color: red; margin-bottom: 10px; white-space: pre-line;"></div>
        
        <form id="form-buku">
            <input type="hidden" id="buku_id"> 
            <div class="form-row">
                <input list="daftar-kategori" class="form-control" id="kategori_id" placeholder="ID Kategori (Pilih 1-5)" required>
                <datalist id="daftar-kategori">
                    <option value="1" label="Fiksi">
                    <option value="2" label="Pelajaran">
                    <option value="3" label="Sejarah">
                    <option value="4" label="Teknologi">
                    <option value="5" label="Sastra">
                </datalist>
                <input type="text" class="form-control" id="judul" placeholder="Judul Buku" required>
                <input type="text" class="form-control" id="penulis" placeholder="Penulis" required>
                <input type="number" class="form-control" id="tahun_terbit" placeholder="Tahun Terbit" min="1901" max="2026" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()">Batal</button>
            </div>
        </form>
    </div>
    @endcan

    <table class="styled-table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Tahun Terbit</th>
                <th>Pinjam</th>
                @can('kelola-buku')
                <th>Kelola</th>
                @endcan
            </tr>
        </thead>
        <tbody id="tabel-buku">
            @forelse ($bukus as $b)
                <tr>
                    <td>{{ $b->kategori->nama ?? $b->kategori_id }}</td>

                    <td><a href="{{ route('buku.show', $b->id) }}">{{ $b->judul }}</a></td>

                    <td>{{ $b->penulis }}</td>
                    <td>{{ $b->tahun_terbit }}</td>
                    <td><a class="btn" href="{{ route('buku.show', $b->id) }}">Detail &amp; Pinjam</a></td>

                    @can('kelola-buku')
                    <td>
                        <button type="button" class="btn btn-warning"
                                data-id="{{ $b->id }}"
                                data-kategori="{{ $b->kategori_id }}"
                                data-judul="{{ $b->judul }}"
                                data-penulis="{{ $b->penulis }}"
                                data-tahun="{{ $b->tahun_terbit }}"
                                onclick="editBuku(this)">Ubah</button>

                        <form method="POST" action="{{ route('buku.destroy', $b->id) }}"
                              style="display:inline"
                              onsubmit="return confirm('Yakin hapus buku ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>

                        {{-- >>> A3 RENTAN (NONAKTIF): hapus baris pembuka dan penutup komentar Blade di sekitar blok ini untuk mengaktifkan
                        <a class="btn btn-danger" href="{{ route('buku.hapus', $b->id) }}"
                           onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                        --}}

                    </td>
                    @endcan
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Belum ada data buku.</td></tr>
            @endforelse
        </tbody>
    </table>

    <script>
    const apiUrl = '{{ url('/api/buku') }}';

    const formBuku = document.getElementById('form-buku');
    if (formBuku) {
        formBuku.addEventListener('submit', async function(e) {
            e.preventDefault();
            document.getElementById('pesan-error').innerText = ''; 
            
            const id = document.getElementById('buku_id').value;
            const inputData = {
                kategori_id: document.getElementById('kategori_id').value,
                judul: document.getElementById('judul').value,
                penulis: document.getElementById('penulis').value,
                tahun_terbit: document.getElementById('tahun_terbit').value
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `${apiUrl}/${id}` : apiUrl;

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'include',
                    body: JSON.stringify(inputData)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (errorData.errors) {
                        let pesanValidasi = '';
                        for (const key in errorData.errors) pesanValidasi += errorData.errors[key][0] + '\n';
                        throw new Error(pesanValidasi);
                    } else {
                        throw new Error(errorData.message || 'Proses gagal.');
                    }
                }

                // muat ulang daftar (sekarang dirender di server)
                window.location.href = '{{ route('buku.index') }}';
            } catch (error) {
                document.getElementById('pesan-error').innerText = error.message;
            }
        });
    }

    function editBuku(btn) {
        if (!document.getElementById('buku_id')) return;
        document.getElementById('buku_id').value = btn.dataset.id;
        document.getElementById('judul').value = btn.dataset.judul;
        document.getElementById('penulis').value = btn.dataset.penulis;
        document.getElementById('tahun_terbit').value = btn.dataset.tahun;
        document.getElementById('kategori_id').value = btn.dataset.kategori;
        document.getElementById('pesan-error').innerText = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        if (!document.getElementById('form-buku')) return;
        document.getElementById('form-buku').reset();
        document.getElementById('buku_id').value = '';
        document.getElementById('pesan-error').innerText = '';
    }
    </script>
@endsection