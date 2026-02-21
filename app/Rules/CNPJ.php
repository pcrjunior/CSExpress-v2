<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CNPJ implements Rule
{
    /**
     * Determina se a validação passa.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $value);

        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais (ex.: 00000000000000)
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Valida o primeiro dígito verificador
        $soma = 0;
        $multiplicadores = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $multiplicadores[$i];
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        // Valida o segundo dígito verificador
        $soma = 0;
        $multiplicadores = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $multiplicadores[$i];
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        return ($cnpj[12] == $digito1 && $cnpj[13] == $digito2);
    }

    /**
     * Mensagem de erro de validação.
     *
     * @return string
     */
    public function message()
    {
        return 'O campo :attribute não contém um CNPJ válido.';
    }
}
