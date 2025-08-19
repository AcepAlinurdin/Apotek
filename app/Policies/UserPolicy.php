<?php

namespace App\Policies;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;


class UserPolicy
{
    use HandlesAuthorization;
     public function update(User $user, User $model): bool
    {
        if ($user->hasRole('admin') && $model->hasRole('admin') && $user->id !== $model->id) {
            return false;
        }
        if ($model->hasRole('kepala apotek') && $user->id !== $model->id) {
            return false;
        }
        return true;
    }
    public function delete(User $user, User $model): bool
    {
        // Aturan 1: Pengguna tidak bisa menghapus dirinya sendiri.
        if ($user->id === $model->id) {
            return false;
        }

        // Aturan 2: Siapapun tidak bisa menghapus Kepala Apotek.
        if ($model->hasRole('kepala apotek')) {
            return false;
        }

        // Aturan 3: Jika targetnya adalah 'admin', yang menghapus HARUS 'kepala apotek'.
        if ($model->hasRole('admin')) {
            return $user->hasRole('kepala apotek');
        }

        // Aturan 4: Jika targetnya adalah 'apoteker', yang menghapus boleh 'admin' ATAU 'kepala apotek'.
        if ($model->hasRole('apoteker')) {
            return $user->hasRole('admin') || $user->hasRole('kepala apotek');
        }
        
        // Gagal secara default jika tidak ada aturan yang cocok.
        return false;
    }

}