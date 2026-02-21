<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserAdminController extends Controller
{

    // Método para exibir a listagem de usuários
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    // Exibe o formulário para criar um novo usuário
    public function create()
    {
        // Aqui, você pode reutilizar a view de registro, mas para o admin é recomendável uma view separada
        return view('usuarios.create');
    }

    // Armazena o novo usuário criado pelo administrador
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'active'   => false, // Define o usuário como ativo por padrão
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Novo usuário criado com sucesso.');
    }

    // Outros métodos (edit, update, toggleActive, destroy) já implementados...

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,{$id}",
        ]);
        $usuario->update($request->only('name', 'email'));
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function toggleActive($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->active = !$usuario->active;
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Status do usuário atualizado.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete(); // Isso setará o deleted_at, sem remover o registro do banco

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuário marcado como excluído com sucesso.');
    }
}

