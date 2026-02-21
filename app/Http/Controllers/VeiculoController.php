<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use Illuminate\Validation\Rule;


class VeiculoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Listagem de veículos
    public function index(Request $request)
    {
        //$veiculos = Veiculo::orderBy('placa')->get();
          $veiculos = Veiculo::query()
        ->when($request->placa, fn($q) => $q->where('placa', 'like', '%' . $request->placa . '%'))
        ->when($request->fabricante, fn($q) => $q->where('fabricante', 'like', '%' . $request->fabricante . '%'))
        ->when($request->modelo, fn($q) => $q->where('modelo', 'like', '%' . $request->modelo . '%'))
        ->when($request->ano_modelo, fn($q) => $q->where('ano_modelo', $request->ano_modelo))
        ->when($request->categoria, fn($q) => $q->where('categoria', 'like', '%' . $request->categoria . '%'))
        ->when($request->dia_rodizio, fn($q) => $q->where('dia_rodizio', 'like', '%' . $request->dia_rodizio . '%'))
        ->orderBy('modelo')
        ->paginate(10)
        ->appends($request->all());

        return view('veiculos.index', compact('veiculos'));
    }

    // Exibe o formulário para criar um novo veículo
    public function create()
    {
        $categorias = Veiculo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $fabricantes = \App\Models\Fabricante::select('id', 'nome')
                            ->distinct()
                            ->orderBy('nome')
                            ->get();
        return view('veiculos.create', compact('fabricantes','categorias'));
    }

    // Armazena o novo veículo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fabricante' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'placa' => [
                'required',
                'regex:/^[A-Z]{3}-[0-9]{4}$|^[A-Z]{3}-[0-9][A-Z][0-9]{2}$/', // AAA-0000 ou AAA-0A00
                Rule::unique('veiculos', 'placa'),
            ],
            'ano_fabricacao' => 'required|integer|min:1900|max:' . date('Y'),
            'ano_modelo' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'categoria' => 'required|string',
            'cor' => 'required|string|max:50',
            'rodizio' => 'required|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

        ],
        [
            'placa.unique' => 'A placa informada já está cadastrada.',
            'placa.regex'  => 'Formato da placa inválido. Use AAA-9999 ou AAA-9A99.',
        ]);

        // Se rodizio for sim, calculamos o dia de rodízio com base no último dígito da placa
        $diaRodizio = null;
        if ($request->rodizio) {
            // Remove hífen e espaços da placa e pega o último caractere (deve ser um número)
            $placaNumerica = preg_replace('/[^0-9]/', '', $request->placa);
            $ultimoDigito = substr($placaNumerica, -1);
            // Regra de rodízio:
            // 1,2 => Segunda; 3,4 => Terça; 5,6 => Quarta; 7,8 => Quinta; 9,0 => Sexta
            if (in_array($ultimoDigito, ['1', '2'])) {
                $diaRodizio = 'Segunda-feira';
            } elseif (in_array($ultimoDigito, ['3', '4'])) {
                $diaRodizio = 'Terça-feira';
            } elseif (in_array($ultimoDigito, ['5', '6'])) {
                $diaRodizio = 'Quarta-feira';
            } elseif (in_array($ultimoDigito, ['7', '8'])) {
                $diaRodizio = 'Quinta-feira';
            } else {
                $diaRodizio = 'Sexta-feira';
            }
        }

        Veiculo::create([
            'placa'          => $request->placa,
            'fabricante'     => $request->fabricante,
            'modelo'         => $request->modelo,
            'ano_fabricacao' => $request->ano_fabricacao,
            'ano_modelo'     => $request->ano_modelo,
            'categoria'      => $request->categoria,
            'cor'            => $request->cor,
            'rodizio'        => $request->rodizio,
            'dia_rodizio'    => $diaRodizio,
        ]);

        return redirect()->route('veiculos.index')
                         ->with('success', 'Veículo cadastrado com sucesso.');
    }

    // Exibe o formulário para editar um veículo
    public function edit($id)
    {
        $categorias = Veiculo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $veiculo = \App\Models\Veiculo::findOrFail($id);
        $fabricantes = \App\Models\Fabricante::select('id', 'nome')
                            ->distinct()
                            ->orderBy('nome')
                            ->get();
        return view('veiculos.edit', compact('veiculo', 'fabricantes','categorias'));
    }

    // Atualiza os dados do veículo
    public function update(Request $request, $id)
    {
        $veiculo = Veiculo::findOrFail($id);
        $request->validate([
            'placa'          => "required|string|unique:veiculos,placa,{$id}",
            'fabricante'     => 'required|string',
            'modelo'         => 'required|string',
            'ano_fabricacao' => 'required|digits:4|integer',
            'ano_modelo'     => 'required|digits:4|integer',
            'categoria'      => 'required|string',
            'cor'            => 'required|string',
            'rodizio'        => 'required|boolean',
        ],
        [
            'placa.unique' => 'A placa informada já está cadastrada.',
            'placa.regex'  => 'Formato da placa inválido. Use AAA-9999 ou AAA-9A99.',
        ]);

        $diaRodizio = null;
        if ($request->rodizio) {
            $placaNumerica = preg_replace('/[^0-9]/', '', $request->placa);
            $ultimoDigito = substr($placaNumerica, -1);
            if (in_array($ultimoDigito, ['1', '2'])) {
                $diaRodizio = 'Segunda-feira';
            } elseif (in_array($ultimoDigito, ['3', '4'])) {
                $diaRodizio = 'Terça-feira';
            } elseif (in_array($ultimoDigito, ['5', '6'])) {
                $diaRodizio = 'Quarta-feira';
            } elseif (in_array($ultimoDigito, ['7', '8'])) {
                $diaRodizio = 'Quinta-feira';
            } else {
                $diaRodizio = 'Sexta-feira';
            }
        }

        $veiculo->update([
            'placa'          => $request->placa,
            'fabricante'     => $request->fabricante,
            'modelo'         => $request->modelo,
            'ano_fabricacao' => $request->ano_fabricacao,
            'ano_modelo'     => $request->ano_modelo,
            'categoria'      => $request->categoria,
            'cor'            => $request->cor,
            'rodizio'        => $request->rodizio,
            'dia_rodizio'    => $diaRodizio,
        ]);

        return redirect()->route('veiculos.index')
                         ->with('success', 'Veículo atualizado com sucesso.');
    }

    // Exclui (soft delete) um veículo
    public function destroy($id)
    {
        $veiculo = Veiculo::findOrFail($id);
        $veiculo->delete();

        return redirect()->route('veiculos.index')
                         ->with('success', 'Veículo excluído com sucesso.');
    }
}
