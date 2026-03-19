<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;
use App\Models\Cliente;
use Illuminate\Support\Str;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => 'required|in:PF,PJ',
            'nome' => 'required|max:100',
            'apelido' => 'nullable|max:50',
            'cep' => 'required|formato_cep',
            'endereco' => 'required|max:100',
            'numero' => 'required|max:20',
            'complemento' => 'nullable|max:100',
            'bairro' => 'nullable|max:100',
            'cidade' => 'required|max:50',
            'uf' => 'required|size:2',
            'responsaveis_data' => 'nullable|json',
            'documento' => 'required|' . ($this->input('tipo') == 'PF' ? 'cpf' : 'cnpj'),
        ];
    }

    public function withValidator(LaravelValidator $validator): void
    {
        $validator->after(function ($validator) {
            $tipo = $this->input('tipo');
            $documento = preg_replace('/\D/', '', $this->input('documento'));
            $apelido = $this->input('apelido') ?? '';
            $apelidoNormalizado = Str::slug($apelido, '');
            $clienteId = $this->route('cliente')?->id;

            // Validar responsáveis
            $responsaveisData = $this->input('responsaveis_data');
            if ($responsaveisData) {
                $responsaveis = json_decode($responsaveisData, true);
                $temResponsavelValido = false;
                
                if (is_array($responsaveis)) {
                    foreach ($responsaveis as $resp) {
                        if (!empty($resp['nome']) || !empty($resp['email'])) {
                            $temResponsavelValido = true;
                            break;
                        }
                    }
                }
                
                if (!$temResponsavelValido) {
                    $validator->errors()->add('responsaveis_data', 'É necessário informar pelo menos um responsável com nome ou e-mail.');
                }
            }

            if ($tipo === 'PF') {
                // Validação CPF único
                $existe = Cliente::withTrashed()
                    ->where('documento', $documento)
                    ->when($clienteId, fn($q) => $q->where('id', '!=', $clienteId))
                    ->exists();

                if ($existe) {
                    $validator->errors()->add('documento', 'Já existe um cliente cadastrado com este CPF.');
                }

            } elseif ($tipo === 'PJ') {
                // Validação CNPJ + Apelido normalizado
                $clientes = Cliente::withTrashed()
                    ->where('documento', $documento)
                    ->get();

                foreach ($clientes as $cliente) {
                    $apelidoExistente = Str::slug($cliente->apelido ?? '', '');
                    if ($apelidoExistente === $apelidoNormalizado && $cliente->id != $clienteId) {
                        $validator->errors()->add('apelido', 'Já existe um cliente PJ com este CNPJ e apelido.');
                        break;
                    }
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'tipo' => 'tipo de cliente',
            'documento' => 'CPF/CNPJ',
            'nome' => 'nome/razão social',
            'apelido' => 'apelido/nome fantasia',
            'cep' => 'CEP',
            'endereco' => 'endereço',
            'numero' => 'número',
            'complemento' => 'complemento',
            'bairro' => 'bairro',
            'cidade' => 'cidade',
            'uf' => 'UF',
            'responsaveis_data' => 'responsáveis',
        ];
    }
}
