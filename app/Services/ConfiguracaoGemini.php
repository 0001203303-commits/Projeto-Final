<?php

namespace App\Services;

use App\Models\Configuracao;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/** Configuração do Gemini: o que foi salvo no painel vale mais que o .env. */
class ConfiguracaoGemini
{
    private const BASE = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function ativo(): bool
    {
        return Configuracao::obter('gemini.ativo', '1') === '1';
    }

    public function chave(): ?string
    {
        return Configuracao::obterCriptografado('gemini.chave') ?: (config('services.gemini.key') ?: null);
    }

    public function origemChave(): string
    {
        if (Configuracao::obterCriptografado('gemini.chave')) {
            return 'painel';
        }

        return config('services.gemini.key') ? '.env' : 'nenhuma';
    }

    public function chaveMascarada(): string
    {
        $chave = $this->chave();

        return $chave ? substr($chave, 0, 3) . '••••••••' . substr($chave, -4) : '—';
    }

    public function modelo(): string
    {
        return Configuracao::obter('gemini.modelo') ?: config('services.gemini.model', 'gemini-3.5-flash');
    }

    public function timeout(): int
    {
        return (int) (Configuracao::obter('gemini.timeout') ?: config('services.gemini.timeout', 15));
    }

    /** Pedido mínimo, só para saber se chave, modelo e rede funcionam. */
    public function testar(): array
    {
        $chave = $this->chave();

        if (! $chave) {
            return ['ok' => false, 'mensagem' => 'Nenhuma chave configurada.'];
        }

        $inicio = microtime(true);
        $ms = fn () => (int) round((microtime(true) - $inicio) * 1000);

        try {
            $r = Http::withHeaders(['x-goog-api-key' => $chave])
                ->timeout($this->timeout())
                ->post(self::BASE . "/{$this->modelo()}:generateContent", [
                    'contents'         => [['parts' => [['text' => 'Responda apenas: ok']]]],
                    'generationConfig' => ['maxOutputTokens' => 64],
                ]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'ms' => $ms(), 'mensagem' => 'Sem conexão com a API do Gemini: ' . Str::limit($e->getMessage(), 160)];
        }

        if ($r->successful()) {
            return ['ok' => true, 'ms' => $ms(), 'mensagem' => "Conexão OK com o modelo {$this->modelo()}."];
        }

        return [
            'ok'       => false,
            'ms'       => $ms(),
            'mensagem' => "Erro {$r->status()}: " . Str::limit($r->json('error.message') ?? 'resposta sem detalhes', 200),
        ];
    }

    /** Modelos de texto disponíveis para a chave atual. */
    public function modelos(): array
    {
        $chave = $this->chave();

        if (! $chave) {
            return ['ok' => false, 'mensagem' => 'Configure uma chave primeiro.', 'modelos' => []];
        }

        try {
            $r = Http::withHeaders(['x-goog-api-key' => $chave])->timeout(15)->get(self::BASE, ['pageSize' => 200]);
        } catch (\Throwable) {
            return ['ok' => false, 'mensagem' => 'Sem conexão com a API do Gemini.', 'modelos' => []];
        }

        if ($r->failed()) {
            return ['ok' => false, 'mensagem' => 'Erro ' . $r->status() . ' ao listar os modelos.', 'modelos' => []];
        }

        $excluir = ['tts', 'image', 'lyria', 'robotics', 'computer-use', 'deep-research', 'antigravity', 'omni', 'transcribe', 'nano-banana', 'customtools', 'embedding', 'live'];

        $modelos = collect($r->json('models', []))
            ->filter(fn ($m) => in_array('generateContent', $m['supportedGenerationMethods'] ?? [], true))
            ->map(fn ($m) => Str::after($m['name'], 'models/'))
            ->filter(fn ($n) => str_starts_with($n, 'gemini') && ! Str::contains($n, $excluir))
            ->sort()
            ->values()
            ->all();

        return ['ok' => true, 'mensagem' => '', 'modelos' => $modelos];
    }
}