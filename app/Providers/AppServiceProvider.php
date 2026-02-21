<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('pt_BR');
        setlocale(LC_TIME, 'Portuguese_Brazil.1252');

        // 🔐 Forçar HTTPS somente quando vier via proxy (Cloudflare)
        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        // ----------------------------
        // Validação de CPF
        // ----------------------------
        Validator::extend('cpf', function ($attribute, $value) {
            $cpf = preg_replace('/[^0-9]/', '', $value);

            if (strlen($cpf) != 11) {
                return false;
            }

            if (preg_match('/(\d)\1{10}/', $cpf)) {
                return false;
            }

            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }
            return true;
        });

        // ----------------------------
        // Validação de CNPJ
        // ----------------------------
        Validator::extend('cnpj', function ($attribute, $value) {
            $cnpj = preg_replace('/[^0-9]/', '', $value);

            if (strlen($cnpj) != 14) {
                return false;
            }

            if (preg_match('/(\d)\1{13}/', $cnpj)) {
                return false;
            }

            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
                $soma += $cnpj[$i] * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;
            if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
                return false;
            }

            for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
                $soma += $cnpj[$i] * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;
            return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
        });

        // CEP
        Validator::extend('formato_cep', function ($attribute, $value) {
            return preg_match('/^[0-9]{5}-?[0-9]{3}$/', $value);
        });

        // Celular com DDD
        Validator::extend('celular_com_ddd', function ($attribute, $value) {
            return preg_match('/^\([1-9]{2}\) [9]{0,1}[0-9]{4}-[0-9]{4}$/', $value);
        });

        // Mensagens customizadas
        Validator::replacer('cpf', fn ($m, $a) => "O campo {$a} não é um CPF válido.");
        Validator::replacer('cnpj', fn ($m, $a) => "O campo {$a} não é um CNPJ válido.");
        Validator::replacer('formato_cep', fn ($m, $a) => "O campo {$a} não está no formato de CEP válido.");
        Validator::replacer('celular_com_ddd', fn ($m, $a) => "O campo {$a} não está no formato de telefone válido.");
    }
}
