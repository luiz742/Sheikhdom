<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos podem visualizar qualquer time.
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true; // Admin global ou admin no time.
        }

        return $user->belongsToTeam($team); // Permite membros do time visualizar.
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return false;
        return $user->role == 1 || $user->hasTeamRole(null, 'admin'); // Admin global ou admin em qualquer time.
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return $user->ownsTeam($team); // Apenas donos podem atualizar o time.
    }

    /**
     * Determine whether the user can add team members.
     */
    public function addTeamMember(User $user, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return $user->ownsTeam($team); // Apenas donos podem adicionar membros.
    }

    /**
     * Determine whether the user can update team member permissions.
     */
    public function updateTeamMember(User $user, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return $user->ownsTeam($team); // Apenas donos podem atualizar permissões de membros.
    }

    /**
     * Determine whether the user can remove team members.
     */
    public function removeTeamMember(User $user, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return $user->ownsTeam($team); // Apenas donos podem remover membros.
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        if ($user->role == 1 || $user->hasTeamRole($team, 'admin')) {
            return true;
        }

        return $user->ownsTeam($team); // Apenas donos podem deletar o time.
    }
}
