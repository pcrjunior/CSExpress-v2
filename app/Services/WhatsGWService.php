<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\OrdemServico;

class WhatsGWService
{
    protected $apiUrl;
    protected $apiKey;
    protected $phoneNumber;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsgw.api_url', 'https://app.whatsgw.com.br/api/WhatsGw/Send');
        $this->apiKey = config('services.whatsgw.api_key');
        $this->phoneNumber = config('services.whatsgw.phone_number');
    }

    /**
     * Envia mensagem de texto via WhatsApp
     *
     * @param string $to Número no formato: 5511999999999
     * @param string $message Mensagem a ser enviada
     * @param string|null $customId ID personalizado para rastreamento
     * @return array
     */
    public function enviarMensagemTexto(string $to, string $message, ?string $customId = null): array
    {
        try {
            // Remove caracteres não numéricos e garante formato correto
            $to = $this->formatarNumero($to);

            // Prepara os parâmetros
            $params = [
                'apikey' => $this->apiKey,
                'phone_number' => $this->phoneNumber,
                'contact_phone_number' => $to,
                'message_type' => 'text',
                'message_body' => $message,
            ];

            // Adiciona ID customizado se fornecido
            if ($customId) {
                $params['message_custom_id'] = $customId;
            }

            // Envia requisição POST
            $response = Http::timeout(30)
                ->post($this->apiUrl, $params);

            $result = $response->json();

            // Verifica se foi enviado com sucesso
            if ($response->successful() && isset($result['result']) && $result['result'] === 'success') {
                Log::info("WhatsApp enviado com sucesso via WhatsGW", [
                    'to' => $to,
                    'message_id' => $result['message_id'] ?? null,
                    'custom_id' => $customId
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['message_id'] ?? null,
                    'phone_state' => $result['phone_state'] ?? null,
                    'data' => $result
                ];
            }

            // Log de erro
            Log::error("Erro ao enviar WhatsApp via WhatsGW", [
                'to' => $to,
                'response' => $result,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Erro desconhecido',
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error("Exceção ao enviar WhatsApp via WhatsGW: " . $e->getMessage(), [
                'to' => $to,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envia documento/arquivo via WhatsApp
     *
     * @param string $to Número de destino
     * @param string $fileUrl URL do arquivo
     * @param string $filename Nome do arquivo
     * @param string $mimeType Tipo MIME (application/pdf, image/jpeg, etc)
     * @param string|null $caption Legenda/descrição
     * @param string|null $customId ID personalizado
     * @return array
     */
    public function enviarDocumento(
        string $to,
        string $fileUrl,
        string $filename,
        string $mimeType = 'application/pdf',
        ?string $caption = null,
        ?string $customId = null
    ): array {
        try {
            $to = $this->formatarNumero($to);

            $params = [
                'apikey' => $this->apiKey,
                'phone_number' => $this->phoneNumber,
                'contact_phone_number' => $to,
                'message_type' => 'document',
                'message_body' => $fileUrl,
                'message_body_mimetype' => $mimeType,
                'message_body_filename' => $filename,
                'download' => 1,
            ];

            if ($caption) {
                $params['message_caption'] = $caption;
            }

            if ($customId) {
                $params['message_custom_id'] = $customId;
            }

            $response = Http::timeout(30)->post($this->apiUrl, $params);
            $result = $response->json();

            if ($response->successful() && isset($result['result']) && $result['result'] === 'success') {
                Log::info("Documento enviado com sucesso via WhatsGW", [
                    'to' => $to,
                    'filename' => $filename,
                    'message_id' => $result['message_id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['message_id'] ?? null,
                    'data' => $result
                ];
            }

            Log::error("Erro ao enviar documento via WhatsGW", [
                'to' => $to,
                'response' => $result
            ]);

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Erro desconhecido',
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error("Exceção ao enviar documento: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Formata número de telefone
     * Remove caracteres especiais e garante formato brasileiro
     *
     * @param string $numero
     * @return string
     */
    private function formatarNumero(string $numero): string
    {
        // Remove tudo exceto números
        $numero = preg_replace('/[^0-9]/', '', $numero);

        // Se tem 11 dígitos (DDD + número), adiciona código do país
        if (strlen($numero) === 11) {
            $numero = '55' . $numero;
        }

        // Se tem 10 dígitos (número antigo sem 9), adiciona 55 + DDD + 9
        if (strlen($numero) === 10) {
            $ddd = substr($numero, 0, 2);
            $resto = substr($numero, 2);
            $numero = '55' . $ddd . '9' . $resto;
        }

        return $numero;
    }

    /**
     * Gera mensagem formatada para nova OS
     *
     * @param OrdemServico $os
     * @return string
     */
    public function gerarMensagemNovaOS(OrdemServico $os): string
    {
        $nomeMotorista = $os->motorista->nome ?? 'Motorista';
        $numeroOS = $os->numero_os;
        $dataServico = \Carbon\Carbon::parse($os->data_servico)->format('d/m/Y');
        $horaInicial = $os->hora_inicial ?? 'A definir';

        // Dados do cliente origem
        $clienteOrigem = $os->clienteOrigem->nome ?? 'Não informado';
        $apelidoOrigem = $os->clienteOrigem->apelido ?? '';
        $enderecoOrigem = trim(($os->clienteOrigem->endereco ?? '') . ', ' . ($os->clienteOrigem->numero ?? ''));
        $bairroOrigem = $os->clienteOrigem->bairro ?? '';
        $cidadeOrigem = $os->clienteOrigem->cidade ?? '';
        $ufOrigem = $os->clienteOrigem->uf ?? '';
        $telefoneOrigem = $os->clienteOrigem->telefone ?? '';

        // Dados do cliente destino
        $clienteDestino = $os->clienteDestino->nome ?? 'Não informado';
        $apelidoDestino = $os->clienteDestino->apelido ?? '';
        $enderecoDestino = trim(($os->clienteDestino->endereco ?? '') . ', ' . ($os->clienteDestino->numero ?? ''));
        $bairroDestino = $os->clienteDestino->bairro ?? '';
        $cidadeDestino = $os->clienteDestino->cidade ?? '';
        $ufDestino = $os->clienteDestino->uf ?? '';
        $telefoneDestino = $os->clienteDestino->telefone ?? '';

        $veiculo = $os->veiculo
            ? "{$os->veiculo->modelo} - {$os->veiculo->placa}"
            : 'Não informado';

/*         $valorMotorista = 'R$ ' . number_format($os->valor_motorista ?? 0, 2, ',', '.');
        $valorRepasse = 'R$ ' . number_format($os->valor_repassado_motorista ?? 0, 2, ',', '.');
 */
        // Lista de ajudantes
        $ajudantes = '';
        if ($os->ajudantes && $os->ajudantes->count() > 0) {
            $ajudantes = "\n\n🤝 *AJUDANTES:*";
            foreach ($os->ajudantes as $ajudante) {
                $ajudantes .= "\n   • " . $ajudante->nome;
                if ($ajudante->telefone) {
                    $ajudantes .= " - " . $ajudante->telefone;
                }
            }
        }

        $observacoes = $os->observacoes
            ? "\n\n📋 *OBSERVAÇÕES:*\n" . $os->observacoes
            : '';

        // Monta a mensagem com formatação WhatsApp
        $mensagem = "🚚 *Expresso Oliveira Santos* 🚚\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $mensagem .= "Olá *{$nomeMotorista}*! 👋\n\n";
        $mensagem .= "Você foi designado para uma nova OS:\n\n";

        $mensagem .= "📄 *OS Nº:* {$numeroOS}\n";
        $mensagem .= "📅 *Data:* {$dataServico}\n";
        $mensagem .= "🕐 *Hora:* {$horaInicial}\n";
        $mensagem .= "🚗 *Veículo:* {$veiculo}\n";

        $mensagem .= "\n━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "📍 *COLETA (ORIGEM)*\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "*Cliente:* {$clienteOrigem}";
        $mensagem .= $apelidoOrigem ? " _{$apelidoOrigem}_" : "";
        $mensagem .= "\n*Endereço:* {$enderecoOrigem}";
        $mensagem .= $bairroOrigem ? "\n*Bairro:* {$bairroOrigem}" : "";
        $mensagem .= "\n*Cidade:* {$cidadeOrigem}/{$ufOrigem}";
        $mensagem .= $telefoneOrigem ? "\n*Telefone:* {$telefoneOrigem}" : "";

        $mensagem .= "\n\n━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "🎯 *ENTREGA (DESTINO)*\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "*Cliente:* {$clienteDestino}";
        $mensagem .= $apelidoDestino ? " _{$apelidoDestino}_" : "";
        $mensagem .= "\n*Endereço:* {$enderecoDestino}";
        $mensagem .= $bairroDestino ? "\n*Bairro:* {$bairroDestino}" : "";
        $mensagem .= "\n*Cidade:* {$cidadeDestino}/{$ufDestino}";
        $mensagem .= $telefoneDestino ? "\n*Telefone:* {$telefoneDestino}" : "";

        $mensagem .= $ajudantes;

    /*     $mensagem .= "\n\n━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "💰 *VALORES*\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "*Valor do Serviço:* {$valorMotorista}\n";
        $mensagem .= "*Seu Repasse:* {$valorRepasse}"; */

        $mensagem .= $observacoes;

        $mensagem .= "\n\n━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "✅ *Boa viagem e bom trabalho!* 🚛💨";

        return $mensagem;
    }

    /**
     * Verifica o status da conexão do WhatsApp
     *
     * @return array
     */
    public function verificarStatus(): array
    {
        try {
            $response = Http::timeout(10)->get($this->apiUrl, [
                'apikey' => $this->apiKey,
                'phone_number' => $this->phoneNumber,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao verificar status WhatsGW: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
