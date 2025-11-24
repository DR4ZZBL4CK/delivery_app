<?php

namespace App\Policies;

use App\Models\Camionero;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CamioneroPolicy
{
    /**
     * Determine whether the user can view any models.
     * Todos los usuarios autenticados pueden ver camioneros.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Todos los usuarios autenticados pueden ver cualquier camionero.
     */
    public function view(User $user, Camionero $camionero): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Solo admins pueden crear camioneros para aislamiento de datos.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     * Solo admins pueden actualizar camioneros para aislamiento de datos.
     */
    public function update(User $user, Camionero $camionero): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     * Solo admins pueden eliminar camioneros para aislamiento de datos.
     */
    public function delete(User $user, Camionero $camionero): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Camionero $camionero): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Camionero $camionero): bool
    {
        return $user->role === 'admin';
    }
}
