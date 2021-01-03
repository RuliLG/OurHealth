<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->is_superadmin || $user->is_hospital_admin;
    }

    public function update(User $user, User $toUpdate)
    {
        // A superadmin can always update
        if ($user->is_superadmin) {
            return true;
        }

        // A hospital admin can only update themselves and other doctors from the same hospital
        if ($user->is_hospital_admin) {
            return $toUpdate->is_doctor && $user->hospital_id === $toUpdate->hospital_id || $toUpdate->id === $user->id;
        }

        return $user->id === $toUpdate->id;
    }

    public function delete(User $user, User $toDelete)
    {
        // A superadmin can always delete
        if ($user->is_superadmin) {
            return true;
        }

        // A hospital admin can only delete themselves and other doctors from the same hospital
        if ($user->is_hospital_admin) {
            return $toDelete->is_doctor && $user->hospital_id === $toDelete->hospital_id || $toDelete->id === $user->id;
        }

        return false;
    }
}
