<?php

namespace App\Policies;

use App\Models\PivotWeaponWeaponAbility;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PivotWeaponWeaponAbilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PivotWeaponWeaponAbility $pivotWeaponWeaponAbility): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PivotWeaponWeaponAbility $pivotWeaponWeaponAbility): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PivotWeaponWeaponAbility $pivotWeaponWeaponAbility): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PivotWeaponWeaponAbility $pivotWeaponWeaponAbility): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PivotWeaponWeaponAbility $pivotWeaponWeaponAbility): bool
    {
        //
    }
}
