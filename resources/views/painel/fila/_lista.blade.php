@php
    $cores = config('triagem.cores');
    $meuId = auth()->id();
    $rotulosStatus = ['aguardando' => 'Aguardando', 'em_atendimento' => 'Em atendimento', 'finalizada' => 'Atendido'];
@endphp

@if ($todas->isEmpty())
    <p class="vazio">Nenhum paciente na fila no momento.</p>
@else
    <div class="lista-cards">
        @foreach ($todas as $t)
            @php
                $info = $cores[$t->cor];
                $limite = $info['limite_min'];
                $queixas = $t->sintomas->pluck('nome')->implode(', ');
                $resumo = $t->resumo_texto;
            @endphp

            <article class="card-paciente {{ $t->alerta_imediato && $t->status === 'aguardando' ? 'com-alerta' : '' }}"
                     data-status="{{ $t->status }}"
                     data-cor="{{ $t->cor }}"
                     data-atendente="{{ $t->atendido_por }}"
                     data-busca="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($t->paciente->nome . ' ' . $queixas . ' ' . str_pad($t->id, 4, '0', STR_PAD_LEFT))) }}">

                <div class="card-topo">
                    <div class="card-id">
                        <span class="selo-cor tom-{{ $t->cor }}"><i></i>{{ $info['descricao'] }}</span>
                        <div>
                            <div class="card-nome">
                                {{ $t->paciente->nome }}
                                @if ($t->alerta_imediato && $t->status !== 'finalizada')<span title="Alerta imediato">⚠️</span>@endif
                            </div>
                            <div class="card-sub">{{ $t->paciente->idade }} anos · {{ $t->paciente->sexo }}</div>
                        </div>
                    </div>

                    <div class="card-acoes">
                        <span class="status-tag status-{{ $t->status }}">
                            {{ $rotulosStatus[$t->status] }}
                            @if ($t->status === 'em_atendimento')
                                · {{ (int) $t->atendido_por === $meuId ? 'você' : ($t->atendente->name ?? '') }}
                            @endif
                        </span>

                        <a class="btn-ver" href="{{ route('painel.fila.mostrar', $t) }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Ver detalhes
                        </a>
                    </div>
                </div>

                <div class="card-resumo">{{ $resumo !== '' ? $resumo : 'Resumo ainda não disponível.' }}</div>

                <div class="card-rodape">
                    <span>Ficha #{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if ($queixas)<span>{{ $queixas }}</span>@endif

                    @if ($t->status === 'aguardando')
                        @if ($limite)
                            @php
                                $pct = min(100, (int) round($t->espera_minutos / $limite * 100));
                                $nivel = $pct >= 100 ? 'estourou' : ($pct >= 70 ? 'perto' : 'ok');
                            @endphp
                            <span class="espera">
                                <span class="barra-espera {{ $nivel }}"><div style="width: {{ $pct }}%"></div></span>
                                <span class="{{ $pct >= 100 ? 'atrasado' : '' }}">{{ $t->espera_minutos }} / {{ $limite }} min</span>
                            </span>
                        @else
                            <span>Espera: {{ $t->espera_minutos }} min</span>
                        @endif
                    @elseif ($t->status === 'em_atendimento' && $t->chamado_em)
                        <span>Em atendimento há {{ (int) floor(abs($t->chamado_em->diffInMinutes(now()))) }} min</span>
                    @elseif ($t->status === 'finalizada' && $t->finalizado_em)
                        <span>Finalizado às {{ $t->finalizado_em->format('H:i') }}</span>
                    @endif

                    @if ($t->comentarios_count > 0)
                        <span>💬 {{ $t->comentarios_count }} {{ $t->comentarios_count === 1 ? 'comentário' : 'comentários' }}</span>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <p class="vazio" id="sem-resultado" hidden>Nenhum paciente com os filtros atuais.</p>
@endif