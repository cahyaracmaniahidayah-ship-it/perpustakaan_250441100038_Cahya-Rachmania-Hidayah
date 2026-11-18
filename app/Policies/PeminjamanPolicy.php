<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * Admin & petugas boleh melihat semua peminjaman;
     * siswa hanya peminjaman miliknya sendiri.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        if ($user->hasAnyRole(['admin', 'petugas'])) {
            return true;
        }

        return $peminjaman->user_id !== null
            && (int) $user->id === (int) $peminjaman->user_id;
    }
}