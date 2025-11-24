<?php

namespace App\Policies;

use App\Models\Paquete;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaquetePolicy
{
    /**
     * Determine whether the user can view any models.
     * Todos los usuarios autenticados pueden ver paquetes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Todos los usuarios autenticados pueden ver cualquier paquete.
     */
    public function view(User $user, Paquete $paquete): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo usuarios autenticados pueden crear paquetes.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Solo admins pueden actualizar paquetes para aislamiento de datos.
     */
    public function update(User $user, Paquete $paquete): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     * Solo admins pueden eliminar paquetes para aislamiento de datos.
     */
    public function delete(User $user, Paquete $paquete): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Paquete $paquete): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Paquete $paquete): bool
    {
        return $user->role === 'admin';
    }
}
