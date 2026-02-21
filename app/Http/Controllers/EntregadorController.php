<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Entregador;
use App\Models\Veiculo;
use App\Rules\CPF;

class EntregadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Entregador::with('veiculos');

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('perfil')) {
            $query->where('perfil', $request->perfil);
        }

        if ($request->filled('cpf')) {
            $query->where('cpf', 'like', '%' . $request->cpf . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('cnh_categoria', 'like', '%' . $request->categoria . '%');
        }

        $entregadores = $query->orderBy('nome')->paginate(10);

        return view('entregadores.index', compact('entregadores'));
    }

    public function create()
    {
        $veiculos = Veiculo::orderBy('placa')->get();
        $veiculosVinculados = [];
        return view('entregadores.create', compact('veiculos', 'veiculosVinculados'));
    }

    public function store(Request $request)
    {

        //dd($request->all());

        $perfil = $request->input('perfil');

        $regras = [
            'nome'     => 'required|string',
            'telefone' => 'nullable|string',
            'email'    => 'nullable|email',
            'perfil'   => 'required|string|in:Motorista,Ajudante',
        ];

        if ($perfil === 'Motorista') {
            $regras += [
                'cpf' => [
                    'nullable',
                    'string',
                    new CPF,
                    Rule::unique('entregadores', 'cpf')->whereNotNull('cpf'),
                ],
                'data_nascimento'=> 'nullable|date',
                'cep'            => 'nullable|string',
                'endereco'       => 'nullable|string',
                'numero'         => 'nullable|string',
                'complemento'    => 'nullable|string',
                'bairro'         => 'nullable|string',
                'cidade'         => 'nullable|string',
                'estado'         => 'nullable|string',
                'cnh_numero'     => 'nullable|string',
                'cnh_validade'   => 'nullable|date',
                'cnh_categoria'  => 'nullable|string',
                'veiculos'       => 'nullable|array',
                'veiculos.*'     => 'exists:veiculos,id',
            ];
        }

        $request->validate($regras);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos_entregadores', 'public');
        }

        $entregador = Entregador::create([
            'nome'           => $request->nome,
            'cpf'            => $request->cpf,
            'data_nascimento'=> $request->data_nascimento,
            'cep'            => $request->cep,
            'endereco'       => $request->endereco,
            'numero'         => $request->numero,
            'complemento'    => $request->complemento,
            'bairro'         => $request->bairro,
            'cidade'         => $request->cidade,
            'estado'         => $request->estado,
            'telefone'       => $request->telefone,
            'email'          => $request->email,
            'cnh_numero'     => $request->cnh_numero,
            'cnh_validade'   => $request->cnh_validade,
            'cnh_categoria'  => $request->cnh_categoria,
            'perfil'         => $request->perfil,
            'active'         => false,
            'foto'           => $fotoPath,
        ]);

        if ($perfil === 'Motorista' && $request->has('veiculos')) {
            $entregador->veiculos()->sync($request->veiculos);
        }

        return redirect()->route('entregadores.index')->with('success', 'Entregador cadastrado com sucesso.');
    }

    public function edit($id)
    {
        $entregador = Entregador::with('veiculos')->findOrFail($id);
        $veiculos = Veiculo::orderBy('placa')->get();
        $veiculosVinculados = $entregador->veiculos->pluck('id')->toArray();
        return view('entregadores.edit', compact('entregador', 'veiculos', 'veiculosVinculados'));
    }

    public function update(Request $request, $id)
    {
        $perfil = $request->input('perfil');

        $regras = [
            'nome'     => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
            'perfil'   => 'required|in:Motorista,Ajudante',
        ];

        if ($perfil === 'Motorista') {
            $regras += [
                'cpf' => [
                    'nullable',
                    'string',
                    new CPF,
                    Rule::unique('entregadores', 'cpf')->ignore($id)->whereNotNull('cpf'),
                ],
                'data_nascimento' => 'nullable|date',
                'cep'             => 'nullable|string',
                'endereco'        => 'nullable|string',
                'numero'          => 'nullable|string',
                'complemento'     => 'nullable|string',
                'bairro'          => 'nullable|string',
                'cidade'          => 'nullable|string',
                'estado'          => 'nullable|string',
                'cnh_numero'      => 'nullable|string',
                'cnh_validade'    => 'nullable|date',
                'cnh_categoria'   => 'nullable|string',
                'veiculos'        => 'nullable|array',
                'veiculos.*'      => 'exists:veiculos,id',
            ];
        }

        $validated = $request->validate($regras);

        $entregador = Entregador::findOrFail($id);

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos_entregadores', 'public');
            $entregador->foto = $fotoPath;
        }

        $entregador->update([
            'nome'            => $request->nome,
            'cpf'             => $request->cpf,
            'data_nascimento' => $request->data_nascimento,
            'cep'             => $request->cep,
            'endereco'        => $request->endereco,
            'numero'          => $request->numero,
            'complemento'     => $request->complemento,
            'bairro'          => $request->bairro,
            'cidade'          => $request->cidade,
            'estado'          => $request->estado,
            'telefone'        => $request->telefone,
            'email'           => $request->email,
            'cnh_numero'      => $request->cnh_numero,
            'cnh_validade'    => $request->cnh_validade,
            'cnh_categoria'   => $request->cnh_categoria,
            'perfil'          => $request->perfil,
        ]);

        if ($perfil === 'Motorista') {
            $entregador->veiculos()->sync($request->input('veiculos', []));
        } else {
            $entregador->veiculos()->detach();
        }

        return redirect()->route('entregadores.index')->with('success', 'Entregador atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $entregador = Entregador::findOrFail($id);
        $entregador->veiculos()->detach();
        $entregador->delete();

        return redirect()->route('entregadores.index')->with('success', 'Entregador excluído com sucesso.');
    }

    public function toggleActive($id)
    {
        $entregador = Entregador::findOrFail($id);
        $entregador->active = !$entregador->active;
        $entregador->save();

        return redirect()->route('entregadores.index')->with('success', 'Status do entregador atualizado com sucesso.');
    }

    public function veiculos($id)
    {
        $entregador = Entregador::with('veiculos')->findOrFail($id);

        if ($entregador->perfil !== 'Motorista') {
            return response()->json(['error' => 'Este entregador não é um Motorista.'], 400);
        }

        return response()->json([
            'entregador' => $entregador->nome,
            'veiculos' => $entregador->veiculos
        ]);
    }

    public function ajudantes()
    {
        $ajudantes = Entregador::where('perfil', 'Ajudante')
            ->select('id', 'nome', 'telefone')
            ->get();

        return response()->json(['ajudantes' => $ajudantes]);
    }

    public function buscarMotoristas(Request $request)
    {
        $termo = $request->input('term');

        $motoristas = Entregador::where('perfil', 'Motorista')
            ->where('nome', 'like', '%' . $termo . '%')
            ->select('id', 'nome as text')
            ->limit(20)
            ->get();

        return response()->json(['results' => $motoristas]);
    }


    public function entregadoresIndex(Request $request)
    {
        $motoristaNome = $request->get('motorista_nome');

        if (!$motoristaNome) {
            return response()->json(['ajudantes' => []]);
        }

        $ajudantes = Entregador::where('perfil', 'Ajudante')
            ->where('deleted_at', null)
            ->where('nome', 'like', $motoristaNome . ' - Ajud %') // ← busca vinculada ao nome do motorista
            ->get(['id', 'nome', 'telefone']);

        return response()->json(['ajudantes' => $ajudantes]);
    }
}



