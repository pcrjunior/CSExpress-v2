<?php

namespace App\Http\Controllers;

use App\Models\Filial;
use Illuminate\Http\Request;
use App\Models\Empresa;


class FilialController extends Controller
{
    // Exibe a listagem de filiais
    public function index()
    {
        $filiais = Filial::all();
        return view('filiais.index', compact('filiais'));
    }

    public function create(Request $request)
    {
        $empresaId = $request->query('empresa_id');

        print_r('$121221');

        if (!$empresaId) {
            return redirect()->back()->with('error', 'Empresa não informada.');
        }

        print_r($empresaId);

        $empresa = Empresa::findOrFail($empresaId);

        return view('filiais.create', compact('empresa'));

    }

    // Processa o armazenamento de uma nova filial
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'      => 'required|string|max:255',
            'cnpj'      => 'required|string|unique:filiais,cnpj',
            'endereco'  => 'required|string',
            'numero'    => 'required|string',
            'bairro'    => 'required|string',
            'cidade'    => 'required|string',
            'estado'    => 'required|string|max:2',
            'cep'       => 'required|string',
        ]);

        Filial::create($validatedData);

        return redirect()->route('filiais.index')->with('success', 'Filial criada com sucesso.');
    }

    // Exibe o formulário de edição
    public function edit(Filial $filial)
    {
        return view('filiais.edit', compact('filial'));
    }

    // Processa a atualização dos dados
    public function update(Request $request, Filial $filial)
    {
        $validatedData = $request->validate([
            'nome'      => 'required|string|max:255',
            'cnpj'      => 'required|string|unique:filiais,cnpj,' . $filial->id,
            'endereco'  => 'required|string',
            'numero'    => 'required|string',
            'bairro'    => 'required|string',
            'cidade'    => 'required|string',
            'estado'    => 'required|string|max:2',
            'cep'       => 'required|string',
        ]);

        $filial->update($validatedData);

        return redirect()->route('filiais.index')->with('success', 'Filial atualizada com sucesso.');
    }

    // Deleta a filial
    public function destroy(Filial $filial)
           
    {
        $filial->delete(); // Isso define o deleted_at, sem remover o registro fisicamente
        return redirect()->route('filiais.index')->with('success', 'Filial removida com sucesso.');
    }
    
}
