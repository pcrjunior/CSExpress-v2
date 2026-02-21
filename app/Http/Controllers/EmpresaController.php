<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Rules\CNPJ;


class EmpresaController extends Controller
{
    public function __construct()
    {
        // Apenas usuários autenticados (e, se necessário, administradores) podem acessar
        $this->middleware(['auth']); // Ajuste o middleware conforme seu sistema
    }

    // Listagem de empresas
    public function index()
    {
        $empresas = Empresa::orderBy('nome')->get();
        return view('empresas.index', compact('empresas'));
    }

    // Exibe o formulário para criar uma nova empresa
    public function create()
    {
        return view('empresas.create');
    }

    // Armazena uma nova empresa
    public function store(Request $request)
    {
        $request->validate([
            'cnpj'         => 'required|string|unique:empresas,cnpj',
            'nome'         => 'required|string',
            'email'        => 'nullable|email',
            'telefone'     => 'nullable|string','cnpj' =>
            ['required', 'string', new CNPJ],

            // Outras validações conforme necessário

        ]);

        // Se houver upload de logomarca, trataremos o arquivo
        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('logomarcas', 'public');
        } else {
            $path = null;
        }

        Empresa::create([
            'cnpj'         => $request->cnpj,
            'nome'         => $request->nome,
            'apelido'      => $request->apelido,
            'email'        => $request->email,
            'nome_contato' => $request->nome_contato,
            'telefone'     => $request->telefone,
            'logomarca'    => $path,
        ]);

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa criada com sucesso.');
    }

    // Exibe o formulário para edição de uma empresa
    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('empresas.edit', compact('empresa'));
    }

    // Atualiza os dados da empresa
    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);
        $request->validate([
            'cnpj'         => "required|string|unique:empresas,cnpj,{$id}",
            'nome'         => 'required|string',
            'email'        => 'nullable|email',
            'telefone'     => 'nullable|string',
        ]);

        

        // Trata o upload da logomarca, se houver
        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('logomarcas', 'public');
        } else {
            $path = $empresa->logomarca;
        }

        $empresa->update([
            'cnpj'         => $request->cnpj,
            'nome'         => $request->nome,
            'apelido'      => $request->apelido,
            'email'        => $request->email,
            'nome_contato' => $request->nome_contato,
            'telefone'     => $request->telefone,
            'logomarca'    => $path,
        ]);

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa atualizada com sucesso.');
    }

    // Exclui (Soft Delete) uma empresa
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();
        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa excluída com sucesso.');
    }


}

