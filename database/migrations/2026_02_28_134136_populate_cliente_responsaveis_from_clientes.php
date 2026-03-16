<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            // RESPONSÁVEL 1
            DB::insert("
                INSERT INTO cliente_responsaveis
                (cliente_id, nome, telefone, email, created_at, updated_at)

                SELECT
                    c.id,
                    LTRIM(RTRIM(c.responsavel)) AS nome,
                    c.telefone,
                    c.email,
                    GETDATE(),
                    GETDATE()
                FROM clientes c
                WHERE c.deleted_at IS NULL
                AND c.responsavel IS NOT NULL
                AND LTRIM(RTRIM(c.responsavel)) <> ''
                AND NOT EXISTS (
                    SELECT 1
                    FROM cliente_responsaveis cr
                    WHERE cr.cliente_id = c.id
                    AND cr.nome = c.responsavel
                )
            ");

            // RESPONSÁVEL 2
            DB::insert("
                INSERT INTO cliente_responsaveis
                (cliente_id, nome, telefone, email, created_at, updated_at)

                SELECT
                    c.id,
                    LTRIM(RTRIM(c.responsavel2)) AS nome,
                    c.telefone2,
                    c.email2,
                    GETDATE(),
                    GETDATE()
                FROM clientes c
                WHERE c.deleted_at IS NULL
                AND c.responsavel2 IS NOT NULL
                AND LTRIM(RTRIM(c.responsavel2)) <> ''
                AND NOT EXISTS (
                    SELECT 1
                    FROM cliente_responsaveis cr
                    WHERE cr.cliente_id = c.id
                    AND cr.nome = c.responsavel2
                )
            ");

        });
    }

    public function down(): void
    {
        DB::transaction(function () {

            DB::delete("
                DELETE cr
                FROM cliente_responsaveis cr
                INNER JOIN clientes c ON c.id = cr.cliente_id
            ");

        });
    }
};
