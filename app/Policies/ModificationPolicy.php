<?php

namespace App\Policies;

use App\Models\Users\User;
use App\Models\Modification;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModificationPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo("read_Modification_data");
        
        
    }

    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, Modification $modification): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can create models.
    //  */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo("create_Modification_data");
    }

    // /**
    //  * Determine whether the user can update the model.
    //  */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo("update_Modification_data");
    }

    // /**
    //  * Determine whether the user can delete the model.
    //  */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo("delete_Modification_data");
    }

    // /**
    //  * Determine whether the user can restore the model.
    //  */
    // public function restore(User $user, Modification $modification): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Modification $modification): bool
    // {
    //     //
    // }
}
