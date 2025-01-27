<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\Jetstream\InviteTeamMember;

class UserController extends Controller
{
    protected $inviteTeamMember;

    // Injeção de dependência para o serviço de convite
    public function __construct(InviteTeamMember $inviteTeamMember)
    {
        $this->inviteTeamMember = $inviteTeamMember;
    }

    public function index()
    {
        $users = User::all();
        $teams = Team::all(); // Carrega todos os times
        return inertia('Admin/Users/Index', ['users' => $users, 'teams' => $teams]);
    }

    public function edit(User $user)
    {
        $teams = Team::all(); // Todos os times
        $userTeams = $user->teams; // Times que o usuário pertence

        return inertia('Admin/Users/Edit', [
            'user' => $user,
            'teams' => $teams,
            'userTeams' => $userTeams,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|integer|min:1|max:3',
            'teams' => 'array', // Validação para os times
        ]);

        // Atualizar dados do usuário
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Atualizar os times aos quais o usuário pertence
        if ($request->has('teams')) {
            $user->teams()->sync($request->input('teams')); // Sincronizar os times
        }

        return redirect()->back()->banner('User Updated.');

    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|integer|min:1|max:3',
        ]);

        $user->update(['role' => $request->role]);
        return back()->banner('success', 'Role updated successfully.');
    }

    public function addUserToTeam(Request $request, Team $team)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', // O email é usado para o convite
            'role' => 'nullable|string',
        ]);

        // Usa o serviço de convite do Jetstream para convidar o usuário
        $this->inviteTeamMember->invite(
            $request->user(), // Usuário autenticado que está enviando o convite
            $team,            // O time ao qual o usuário será adicionado
            $request->input('email'), // Email do usuário convidado
            $request->input('role', 'member') // Papel padrão (member ou outro)
        );

        return redirect()->back()->banner('message', 'Invitation sent successfully.');
    }
}
