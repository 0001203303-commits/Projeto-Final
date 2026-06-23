@extends('admin.index')

@section('conteudo')
<style>
    .form-cadastro-container { display: none; margin-bottom: 24px; padding: 24px; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    .form-editar-expanded form, .form-cadastro-container form { max-width: 100%; margin: 0; padding: 10px; background: transparent; }
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 12px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px; }
    
    form div { display: flex; flex-direction: column; margin-bottom: 4px; }
    form label { font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
    form input, form select, form textarea { width: 100%; padding: 8px 12px; font-size: 0.875rem; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; transition: border-color 0.15s ease-in-out; }
    form input:focus, form select:focus, form textarea:focus { outline: none; border-color: #3b82f6; }
    form textarea { min-height: 70px; resize: vertical; font-family: inherit; }
    
    .form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; grid-column: span 3; }
    form button { padding: 8px 16px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; cursor: pointer; border: none; transition: background 0.2s; }
    form button[type="submit"] { background: #10b981; color: #ffffff; }
    form button[type="submit"]:hover { background: #059669; }
    
    .btn-cancelar { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-cancelar:hover { background: #e2e8f0; }
    .btn-novo { background: #3b82f6; color: #fff; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; transition: background 0.2s; }
    .btn-novo:hover { background: #2563eb; }

    .content-box { background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); margin-top: 20px; width: 100%; }
    .table-pacientes { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
    .table-pacientes th { background-color: #f8fafc; color: #64748b; font-weight: 600; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 0.75rem; }
    .table-pacientes td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; color: #334155; vertical-align: middle; }
    .table-pacientes tbody tr:not(.form-editar-expanded):hover { background-color: #f8fafc; }

    .btn-acao-editar { padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-size: 0.8rem; font-weight: 500; transition: all 0.2s; }
    .btn-acao-editar:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-acao-remover { padding: 6px 12px; border-radius: 6px; border: 1px solid #fee2e2; background: #ef4444; color: #ffffff; cursor: pointer; font-size: 0.8rem; font-weight: 500; transition: background 0.2s; }
    .btn-acao-remover:hover { background: #dc2626; }

    .box-sintomas-totem { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 8px; max-height: 150px; overflow-y: auto; }
    .item-sintoma-totem { font-size: 0.8rem; margin-bottom: 6px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 4px; }
    .item-sintoma-totem:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    
    @media (max-width: 768px) { 
        .form-grid-3, .form-grid-2 { grid-template-columns: 1fr; gap:2px; } 
        .header { flex-direction: column; align-items: flex-start !important; gap: 14px; }
        .header div { width: 100%; }
        .search-box input { width: 100%; }
    }
</style>

<main class="main-content">
    <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="color: #1e293b; margin: 0 0 4px 0; font-size: 1.75rem;">Gestão de Pacientes</h1>
            <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Controle de fila e classificação de risco (Manchester).</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div class="search-box">
                <input type="text" id="inputBusca" placeholder="Buscar por nome..." onkeyup="filtrarTabela()" style="padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem; outline: none;">
            </div>
            <button class="btn-novo" onclick="toggleFormCadastro()">+ Novo Paciente</button>
        </div>
    </div>

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-weight: 600;">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-weight: 600;">
            {{ session('success') }}
        </div>
    @endif

    <div id="containerCadastro" class="form-cadastro-container">
        <h3 style="margin-top:0; margin-bottom: 20px; color:#1e293b; font-size: 1.2rem;">Cadastrar Novo Paciente</h3>
        <form action="{{ route('pacientes.salvar') }}" method="POST">
            @csrf 
            <input type="hidden" name="id" value="">

            <div style="margin-bottom: 12px;">
                <label for="new-nome">Nome Completo:</label>
                <input type="text" id="new-nome" name="nome" required>
            </div>

            <div class="form-grid-3">
                <div>
                    <label for="new-cpf">CPF:</label>
                    <input type="text" id="new-cpf" name="cpf" class="mascara-cpf" placeholder="000.000.000-00" required>
                </div>
                <div>
                    <label for="new-protocolo">Protocolo:</label>
                    <input type="text" id="new-protocolo" name="protocolo" class="mascara-protocolo" placeholder="#000000000" required>
                </div>
                <div>
                    <label for="new-telefone">Telefone:</label>
                    <input type="tel" id="new-telefone" name="telefone" class="mascara-telefone" placeholder="(00) 00000-0000" required>
                </div>
            </div>

            <div class="form-grid-3">
                <div>
                    <label for="new-data_nascimento">Nascimento:</label>
                    <input type="date" id="new-data_nascimento" name="data_nascimento">
                </div>
                <div>
                    <label for="new-idade">Idade:</label>
                    <input type="number" id="new-idade" name="idade">
                </div>
                <div>
                    <label for="new-sexo">Sexo:</label>
                    <select id="new-sexo" name="sexo">
                        <option value="">Selecione...</option>
                        <option value="M">M</option>
                        <option value="F">F</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-3">
                <div>
                    <label for="new-temperatura">Temperatura:</label>
                    <input type="number" id="new-temperatura" name="temperatura" step="0.1">
                </div>
                <div>
                    <label for="new-bpm">BPM:</label>
                    <input type="number" id="new-bpm" name="bpm" step="1">
                </div>
                <div>
                    <label for="new-oxigenacao">Oxigenação:</label>
                    <input type="number" id="new-oxigenacao" name="oxigenacao" step="1">
                </div>
            </div>

            <div class="form-grid-2">
                <div>
                    <label for="new-tipo_sanguineo">Tipo Sanguíneo:</label>
                    <select id="new-tipo_sanguineo" name="tipo_sanguineo">
                        <option value="">...</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="new-urgencia">Classificação (Urgência):</label>
                    <select id="new-urgencia" name="urgencia">
                        <option value="pouca">Mínima Urgência (Azul)</option>
                        <option value="baixa" selected>Pouco Urgente (Verde)</option>
                        <option value="media">Urgente (Amarelo)</option>
                        <option value="muito_urgente">Muito Urgente (Laranja)</option>
                        <option value="alta">Emergência (Vermelho)</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div>
                    <label for="new-sintoma">Sintomas:</label>
                    <textarea id="new-sintoma" name="sintoma"></textarea>
                </div>
                <div>
                    <label for="new-antecedentes_pessoais">Antecedentes:</label>
                    <textarea id="new-antecedentes_pessoais" name="antecedentes_pessoais"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancelar" onclick="toggleFormCadastro()">Cancelar</button>
                <button type="submit">Cadastrar Paciente</button>
            </div>
        </form>
    </div>

    <div class="content-box">
        <table id="tabelaPacientes" class="table-pacientes">
            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Paciente</th>
                    <th>Idade</th>
                    <th>Chegada</th>
                    <th>Prioridade</th>
                    <th>Sintomas</th>
                    <th style="width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($Pacientes as $p)
                    @php
                        $urgencia = $p->urgencia ?? 'baixa';
                        
                        // Lógica corrigida do Protocolo de Manchester
                        if (str_contains(strtoupper($urgencia), 'VERMELHO') || strtoupper($urgencia) == 'ALTA') {
                            $bgCor = '#f87171'; $textoCor = '#b91c1c'; $label = 'Emergência';
                        } elseif (str_contains(strtoupper($urgencia), 'LARANJA') || strtoupper($urgencia) == 'MUITO_URGENTE') {
                            $bgCor = '#f97316'; $textoCor = '#7c2d12'; $label = 'Muito Urgente';
                        } elseif (str_contains(strtoupper($urgencia), 'AMARELO') || strtoupper($urgencia) == 'MEDIA') {
                            $bgCor = '#fbbf24'; $textoCor = '#b45309'; $label = 'Urgente';
                        } elseif (str_contains(strtoupper($urgencia), 'VERDE') || strtoupper($urgencia) == 'BAIXA') {
                            $bgCor = '#34d399'; $textoCor = '#065f46'; $label = 'Pouco Urgente';
                        } elseif (str_contains(strtoupper($urgencia), 'AZUL') || strtoupper($urgencia) == 'POUCA') {
                            $bgCor = '#60a5fa'; $textoCor = '#1e3a8a'; $label = 'Mínima Urgência';
                        } else {
                            $bgCor = '#e5e7eb'; $textoCor = '#374151'; $label = 'Sem classificação';
                        }

                        // Tenta decodificar o JSON dos sintomas
                        $sintomasRaw = $p->sintomas ?? $p->sintoma ?? '';
                        $sintomasJson = json_decode($sintomasRaw, true);
                        $isJson = (json_last_error() === JSON_ERROR_NONE && is_array($sintomasJson));
                    @endphp
                    <tr>
                        <td><strong>#{{ $p->protocolo ?? '---' }}</strong></td>
                        <td><span style="color: #1e293b; font-weight: 600;">{{ $p->nome ?? 'Sem Nome' }}</span></td>
                        <td>{{ $p->idade ?? '0' }} anos</td>
                        <td>{{ isset($p->created_at) ? $p->created_at->format('H:i') : '-' }}</td>
                        <td>
                            <span class="badge" style="background: {{ $bgCor }}; color: {{ $textoCor }}; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.70rem; letter-spacing: 0.05em; display: inline-block;">
                                {{ $label }}
                            </span>
                        </td>
                        <td style="max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            @if($isJson)
                                @php 
                                    // Pega apenas as respostas "Sim" para resumir na tabela
                                    $resumoSintomas = [];
                                    foreach($sintomasJson as $item) {
                                        if(($item['resposta'] ?? '') == 'Sim') {
                                            $resumoSintomas[] = str_replace(['Você ', 'Sua ', 'Seu '], '', $item['pergunta']);
                                        }
                                    }
                                @endphp
                                <span style="color: #10b981; font-weight: 500;">[Totem]:</span> 
                                {{ count($resumoSintomas) > 0 ? implode(', ', $resumoSintomas) : 'Respondeu não a tudo' }}
                            @else
                                {{ $sintomasRaw ?: 'Nenhum' }}
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                @if(isset($p->id))
                                    <button type="button" class="btn-acao-editar" onclick="toggleForm({{ $p->id }})">
                                        Editar
                                    </button>
                                    <button type="button" class="btn-acao-remover" onclick="if(confirm('Tem certeza que deseja remover este paciente?')) { window.location.href='{{ route('pacientes.deletar', $p->id) }}'; }">
                                        Remover
                                    </button>   
                                @endif
                            </div>
                        </td>
                    </tr>

                    @if(isset($p->id))
                    <tr id="form-editar-{{ $p->id }}" class="form-editar-expanded" style="display: none; background-color: #f8fafc;">
                        <td colspan="7" style="padding: 16px 0; border-bottom: 1px solid #cbd5e1; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.06);">
                            <div style="background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 0 16px;">
                                <h4 style="margin-top: 0; margin-bottom: 16px; color: #334155;">Editar Cadastro do Paciente</h4>
                                <form action="{{ route('pacientes.salvar') }}" method="POST">
                                    @csrf 
                                    <input type="hidden" name="id" value="{{ $p->id }}">

                                    <div style="margin-bottom: 12px;">
                                        <label for="nome-{{ $p->id }}">Nome Completo:</label>
                                        <input type="text" id="nome-{{ $p->id }}" name="nome" value="{{ $p->nome ?? '' }}" required>
                                    </div>

                                    <div class="form-grid-3">
                                        <div>
                                            <label for="cpf-{{ $p->id }}">CPF:</label>
                                            <input type="text" id="cpf-{{ $p->id }}" name="cpf" class="mascara-cpf" value="{{ $p->cpf ?? '' }}" placeholder="000.000.000-00" required>
                                        </div>
                                        <div>
                                            <label for="protocolo-{{ $p->id }}">Protocolo:</label>
                                            <input type="text" id="protocolo-{{ $p->id }}" name="protocolo" class="mascara-protocolo" value="{{ $p->protocolo ?? '' }}" placeholder="#000000000" required>
                                        </div>
                                        <div>
                                            <label for="telefone-{{ $p->id }}">Telefone:</label>
                                            <input type="tel" id="telefone-{{ $p->id }}" name="telefone" class="mascara-telefone" value="{{ $p->telefone ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-grid-3">
                                        <div>
                                            <label for="data_nascimento-{{ $p->id }}">Nascimento:</label>
                                            <input type="date" id="data_nascimento-{{ $p->id }}" name="data_nascimento" value="{{ $p->data_nascimento ?? '' }}">
                                        </div>
                                        <div>
                                            <label for="idade-{{ $p->id }}">Idade:</label>
                                            <input type="number" id="idade-{{ $p->id }}" name="idade" value="{{ $p->idade ?? '' }}">
                                        </div>
                                        <div>
                                            <label for="sexo-{{ $p->id }}">Sexo:</label>
                                            <select id="sexo-{{ $p->id }}" name="sexo">
                                                <option value="">Selecione...</option>
                                                <option value="M" {{ ($p->sexo ?? '') == 'M' ? 'selected' : '' }}>M</option>
                                                <option value="F" {{ ($p->sexo ?? '') == 'F' ? 'selected' : '' }}>F</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-grid-3">
                                        <div>
                                            <label for="temperatura-{{ $p->id }}">Temperatura:</label>
                                            <input type="number" id="temperatura-{{ $p->id }}" name="temperatura" value="{{ $p->temperatura ?? '' }}" step="0.1">
                                        </div>
                                        <div>
                                            <label for="bpm-{{ $p->id }}">BPM:</label>
                                            <input type="number" id="bpm-{{ $p->id }}" name="bpm" value="{{ $p->bpm ?? '' }}" step="1">
                                        </div>
                                        <div>
                                            <label for="oxigenacao-{{ $p->id }}">Oxigenação:</label>
                                            <input type="number" id="oxigenacao-{{ $p->id }}" name="oxigenacao" value="{{ $p->oxigenacao ?? '' }}" step="1">
                                        </div>
                                    </div>

                                    <div class="form-grid-2">
                                        <div>
                                            <label for="tipo_sanguineo-{{ $p->id }}">Tipo Sanguíneo:</label>
                                            <select id="tipo_sanguineo-{{ $p->id }}" name="tipo_sanguineo">
                                                <option value="">...</option>
                                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                                    <option value="{{ $tipo }}" {{ ($p->tipo_sanguineo ?? '') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="urgencia-{{ $p->id }}">Classificação (Urgência):</label>
                                            <select id="urgencia-{{ $p->id }}" name="urgencia">
                                                <option value="pouca" {{ $urgencia == 'pouca' || str_contains(strtoupper($urgencia), 'AZUL') ? 'selected' : '' }}>Mínima Urgência (Azul)</option>
                                                <option value="baixa" {{ $urgencia == 'baixa' || str_contains(strtoupper($urgencia), 'VERDE') ? 'selected' : '' }}>Pouco Urgente (Verde)</option>
                                                <option value="media" {{ $urgencia == 'media' || str_contains(strtoupper($urgencia), 'AMARELO') ? 'selected' : '' }}>Urgente (Amarelo)</option>
                                                <option value="muito_urgente" {{ $urgencia == 'muito_urgente' || str_contains(strtoupper($urgencia), 'LARANJA') ? 'selected' : '' }}>Muito Urgente (Laranja)</option>
                                                <option value="alta" {{ $urgencia == 'alta' || str_contains(strtoupper($urgencia), 'VERMELHO') ? 'selected' : '' }}>Emergência (Vermelho)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-grid-2">
                                        <div>
                                            <label>Sintomas / Triagem Eletrônica:</label>
                                            @if($isJson)
                                                <div class="box-sintomas-totem">
                                                    @foreach($sintomasJson as $item)
                                                        <div class="item-sintoma-totem">
                                                            <strong>Q:</strong> {{ $item['pergunta'] }} <br>
                                                            <strong>R:</strong> <span style="color: {{ $item['resposta'] == 'Sim' ? '#ef4444' : '#3b82f6' }}; font-weight: bold;">{{ $item['resposta'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <input type="hidden" name="sintoma" value="{{ $sintomasRaw }}">
                                                <span style="font-size:0.75rem; color:#64748b; font-style:italic;">* Dados importados do Totem de Triagem (Não editável manualmente).</span>
                                            @else
                                                <textarea id="sintoma-{{ $p->id }}" name="sintoma">{{ $sintomasRaw }}</textarea>
                                            @endif
                                        </div>
                                        <div>       
                                            <label for="antecedentes_pessoais-{{ $p->id }}">Antecedentes:</label>
                                            <textarea id="antecedentes_pessoais-{{ $p->id }}" name="antecedentes_pessoais">{{ $p->antecedentes_pessoais ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="button" class="btn-cancelar" onclick="toggleForm({{ $p->id }})">Cancelar</button>
                                        <button type="submit" style="background: #3b82f6; color: white;">Salvar Alterações</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #64748b; font-size: 0.95rem;">Nenhum paciente na fila.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<script src="https://unpkg.com/imask"></script>
<script>
    function toggleFormCadastro() {
        const box = document.getElementById("containerCadastro");
        box.style.display = box.style.display === "block" ? "none" : "block";
    }

    function toggleForm(id) {
        const formulario = document.getElementById('form-editar-' + id);
        
        if (formulario) {
            if (formulario.style.display === 'none' || formulario.style.display === '') {
                formulario.style.display = 'table-row';
            } else {
                formulario.style.display = 'none'; 
            }
        } else {
            console.error('Erro: Não foi encontrado nenhum formulário com o ID form-editar-' + id);
        }
    }

    function filtrarTabela() {
        const input = document.getElementById("inputBusca");
        const filter = input.value.toUpperCase();
        const tr = document.querySelectorAll("#tabelaPacientes tbody tr:not(.form-editar-expanded)");

        tr.forEach(row => {
            const texto = row.textContent.toUpperCase();
            const proximaLinhaForm = row.nextElementSibling;
            
            if (texto.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
                if (proximaLinhaForm && proximaLinhaForm.classList.contains('form-editar-expanded')) {
                    proximaLinhaForm.style.display = "none";
                }
            }
        });
    }

    function aplicarMascaras() {
        const cpfs = document.querySelectorAll('.mascara-cpf');
        cpfs.forEach(input => {
            if (!input.dataset.maskApplied) {
                IMask(input, { mask: '000.000.000-00' });
                input.dataset.maskApplied = true; 
            }
        });

        const telefones = document.querySelectorAll('.mascara-telefone');
        telefones.forEach(input => {
            if (!input.dataset.maskApplied) {
                IMask(input, { mask: '(00) 00000-0000' });
                input.dataset.maskApplied = true;
            }
        }); 
        
        const protocolos = document.querySelectorAll('.mascara-protocolo');
        protocolos.forEach(input => {
            if (!input.dataset.maskApplied) {
                // Removido o caractere '#' do padrão da máscara para evitar que o IMask bloqueie a digitação
                IMask(input, { mask: '000000000' });
                input.dataset.maskApplied = true;
            }
        }); 
    };

    document.addEventListener("DOMContentLoaded", aplicarMascaras);
</script>
@endsection