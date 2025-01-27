<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use App\Models\Team;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense, ?Team $team = null): bool
    {
        // Se o time não for passado, use o time da despesa
        $team = $team ?? $expense->team;

        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true; // Admin global ou admin no time
        }

        return $user->belongsToTeam($team);
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true; // Admin global ou admin no time.
        }

        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return false;
    }
}
