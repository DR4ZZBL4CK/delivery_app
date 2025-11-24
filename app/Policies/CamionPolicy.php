<?php

namespace App\Policies;

use App\Models\Camion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CamionPolicy
{
    /**
     * Determine whether the user can view any models.
     * Todos los usuarios autenticados pueden ver camiones.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Todos los usuarios autenticados pueden ver cualquier camión.
     */
    public function view(User $user, Camion $camion): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo admins pueden crear camiones para aislamiento de datos.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     * Solo admins pueden actualizar camiones para aislamiento de datos.
     */
    public function update(User $user, Camion $camion): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     * Solo admins pueden eliminar camiones para aislamiento de datos.
     */
    public function delete(User $user, Camion $camion): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Camion $camion): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Camion $camion): bool
    {
        return $user->role === 'admin';
    }
}
