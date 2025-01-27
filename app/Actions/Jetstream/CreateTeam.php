<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Events\AddingTeam;
use Laravel\Jetstream\Jetstream;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Team
    {
        // Garante que o usuário tem permissão para criar um Team
        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        // Valida os dados de entrada
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createTeam');

        // Evento para adicionar um Team
        AddingTeam::dispatch($user);

        // Cria o Team manualmente
        $team = $user->ownedTeams()->create([
            'name' => $input['name'], // Nome fornecido no input
            'personal_team' => false, // Define como não pessoal por padrão
        ]);

        // Define o Team criado como o ativo
        $user->switchTeam($team);

        return $team;
    }
}
