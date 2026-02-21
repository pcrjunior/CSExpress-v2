<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CPF implements Rule
{
    public function passes($attribute, $value)
    {
        // Remove qualquer caractere não numérico
        $cpf = preg_replace('/[^0-9]/', '', $value);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais (ex.: 00000000000)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Valida os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$t] != $d) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'O campo :attribute não contém um CPF válido.';
    }
}
