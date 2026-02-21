<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */



     public function create()
     {
         return view('clientes.create');
     }

    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('documento')) {
            $documento = preg_replace('/\D/', '', $request->documento);
            $query->where('documento', 'like', '%' . $documento . '%');
        }

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('apelido')) {
            $query->where('apelido', 'like', '%' . $request->apelido . '%');
        }

        if ($request->filled('telefone')) {
            $query->where('telefone', 'like', '%' . $request->telefone . '%');
        }

        $clientes = $query->orderBy('nome')->paginate(10)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }


    public function store(ClienteRequest $request)

    {
        $dados = $request->validated();
        Cliente::create($dados);
        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $dados = $request->validated();
        $cliente->update($dados);
        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso.');
    }


    public function getDadosCliente(Request $request, $clienteId = null)
    {

        if (!$clienteId) {
            return response()->json(['cliente' => null]);
        }

        $cliente = Cliente::findOrFail($clienteId);

        return response()->json([
            'cliente' => [
                'id' => $cliente->id,
                'responsavel' => $cliente->responsavel,
                'telefone' => $cliente->telefone,
                'email' => $cliente->email,
                'cep' => $cliente->cep,
                'endereco' => $cliente->endereco,
                'bairro' => $cliente->bairro,
                'cidade' => $cliente->cidade,
                'uf' => $cliente->uf,
                'cidade_uf' => $cliente->cidade . '/' . $cliente->uf,
                'responsavel2' => $cliente->responsavel2,
                'telefone2' => $cliente->telefone2,
                'email2' => $cliente->email2,
            ]
        ]);

    }


    public function buscaPorApelido(Request $request)
    {
        $apelido = $request->input('apelido');

        if (empty($apelido)) {
            return response()->json([]);
        }

        // Buscar por apelido ou nome que contenha a string fornecida
        $clientes = Cliente::where('apelido', 'LIKE', "%{$apelido}%")
                            ->orWhere('nome', 'LIKE', "%{$apelido}%")
                            ->select('id', 'nome', 'apelido', 'telefone', 'email')
                            ->limit(10)
                            ->get();

        return response()->json($clientes);
    }


    public function listarTodos()
    {
        try {
            $clientes = Cliente::orderBy('nome')->get();
            return response()->json($clientes);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeAvulso(Request $request)
    {
        $request->validate([
            'nome_avulso'     => 'required|string|max:255',
            'telefone_avulso' => 'required|string|max:20',
            'endereco_avulso' => 'required|string|max:255',
            'numero_avulso'   => 'required|string|max:50',
        ]);

        // Normaliza o endereço para comparação
        $enderecoLimpo = preg_replace('/[^a-zA-Z0-9]/','',strtolower($request->endereco_avulso));
        $numeroLimpo   = preg_replace('/[^0-9]/', '', $request->numero_avulso);


        $clienteExistente = Cliente::where('avulso', true)->get()
        ->first(function ($cliente) use ($enderecoLimpo, $numeroLimpo) {
            $enderecoCliente = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($cliente->endereco));
            $numeroCliente   = preg_replace('/[^0-9]/', '', $cliente->numero);

            return $enderecoCliente === $enderecoLimpo && $numeroCliente === $numeroLimpo;
        });

        if ($clienteExistente) {
            return response()->json([
                'exists' => true,
                'cliente' => $clienteExistente
            ]);
        }

        $cliente = Cliente::create([
            'nome'       => $request->nome_avulso,
            'apelido'    => $request->nome_avulso,
            'tipo'       => 'PJ',
            'documento'  => '00000000000000',
            'telefone'   => $request->telefone_avulso,
            'endereco'   => $request->endereco_avulso,
            'numero'     => $request->numero_avulso,
            'cep'        => '00000000',
            'cidade'     => '',
            'uf'         => '',
            'avulso'     => true,
            'email'      => ''
        ]);

        return response()->json([
            'created' => true,
            'cliente' => $cliente
        ]);
    }

}
