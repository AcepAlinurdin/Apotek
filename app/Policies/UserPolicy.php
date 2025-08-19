<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah pengguna dapat mengupdate model.
     *
     * @param \App\Models\User $user  // Pengguna yang sedang login (yang melakukan aksi)
     * @param \App\Models\User $model // Pengguna yang akan diedit
     */
     public function update(User $user, User $model): bool
    {
        // ATURAN 1: Sesama admin tidak boleh saling mengedit.
        // Izin ditolak jika pengguna yang login adalah 'admin',
        // target yang diedit juga 'admin', DAN ID mereka berbeda.
        if ($user->hasRole('admin') && $model->hasRole('admin') && $user->id !== $model->id) {
            return false;
        }
        
        // ATURAN 2: Siapapun tidak boleh mengedit 'kepala apotek',
        // kecuali kepala apotek itu sendiri.
        if ($model->hasRole('kepala apotek') && $user->id !== $model->id) {
            return false;
        }

        // Jika tidak ada aturan di atas yang melarang, maka izinkan.
        return true;
    }

    // Anda bisa menambahkan method lain seperti 'delete', 'create', dll. di sini.
}