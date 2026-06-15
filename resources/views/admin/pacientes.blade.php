@extends('admin.index')

@section('conteudo')
<style>
    /* Estilos das Grids e Formações do Formulário */
    .form-cadastro-container { display: none; margin-bottom: 24px; padding: 24px; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    .form-row-expanded form, .form-cadastro-container form { max-width: 100%; margin: 0; padding: 10px; background: transparent; }
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 12px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px; }
    
    form div { display: flex; flex-direction: column; margin-bottom: 4px; }
    form label { font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
    form input, form select, form textarea { width: 100%; padding: 8px 12px; font-size: 0.875rem; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; transition: border-color 0.15s ease-in-out; }
    form input:focus, form select:focus, form textarea:focus { outline: none; border-color: #3b82f6; ring: 2px solid #bfdbfe; }
    form textarea { min-height: 70px; resize: vertical; font-family: inherit; }
    
    .form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; grid-column: span 3; }
    form button { padding: 8px 16px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; cursor: pointer; border: none; transition: background 0.2s; }
    form button[type="submit"] { background: #10b981; color: #ffffff; }
    form button[type="submit"]:hover { background: #059669; }
    
    .btn-cancelar { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-cancelar:hover { background: #e2e8f0; }
    .btn-novo { background: #3b82f6; color: #fff; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; transition: background 0.2s; }
    .btn-novo:hover { background: #2563eb; }

    /* Estilização Moderna da Tabela */
    .content-box { background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); margin-top: 20px; }
    .table-pacientes { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
    .table-pacientes th { background-color: #f8fafc; color: #64748b; font-weight: 600; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 0.75rem; }
    .table-pacientes td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; color: #334155; vertical-align: middle; }
    .table-pacientes tbody tr:not(.form-row-expanded):hover { background-color: #f8fafc; }

    /* Estilização dos botões de ação da tabela */
    .btn-acao-editar { padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-size: 0.8rem; font-weight: 500; transition: all 0.2s; }
    .btn-acao-editar:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-acao-remover { padding: 6px 12px; border-radius: 6px; border: 1px solid #fee2e2; background: #ef4444; color: #ffffff; cursor: pointer; font-size: 0.8rem; font-weight: 500; transition: background 0.2s; }
    .btn-acao-remover:hover { background: #dc2626; }
    
    @media (max-width: 768px) { 
        .form-grid-3, .form-grid-2 { grid-template-columns: 1fr; gap: 12px; } 
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

    <div id="containerCadastro" class="form-cadastro-container">
        <h3 style="margin-top:0; margin-bottom: 20px; color:#1e293b; font-size: 1.2rem;">Cadastrar Novo Paciente</h3>
        <form action="{{ route('pacientes.salvar') }}" method="POST">
            @csrf 
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
                        <option value="baixa">Pouco Urgente (Verde)</option>
                        <option value="media">Urgente (Amarelo)</option>
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
                @forelse($pacientes ?? [] as $paciente)
                    @php
                        $urgencia = $paciente->urgencia ?? 'baixa';
                        switch($urgencia) {
                            case 'alta':
                                $bgCor = '#fee2e2'; $textoCor = '#ef4444'; $label = 'EMERGÊNCIA';
                                break;
                            case 'media':
                                $bgCor = '#fef3c7'; $textoCor = '#d97706'; $label = 'URGENTE';
                                break;
                            case 'baixa':
                            default:
                                $bgCor = '#d1fae5'; $textoCor = '#10b981'; $label = 'POUCO URGENTE';
                                break;
                        }
                    @endphp
                    
                    <tr>
                        <td><strong>#{{ $paciente->protocolo ?? '---' }}</strong></td>
                        <td><span style="color: #1e293b; font-weight: 600;">{{ $paciente->nome ?? 'Sem Nome' }}</span></td>
                        <td>{{ $paciente->idade ?? '0' }} anos</td>
                        <td>{{ isset($paciente->created_at) ? $paciente->created_at->format('H:i') : '-' }}</td>
                        <td>
                            <span class="badge" style="background: {{ $bgCor }}; color: {{ $textoCor }}; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.70rem; letter-spacing: 0.05em;">
                                {{ $label }}
                            </span>
                        </td>
                        <td style="max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $paciente->sintomas ?? $paciente->sintoma ?? 'Nenhum' }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                @if(isset($paciente->id))
                                    <button type="button" class="btn-acao-editar" onclick="toggleForm({{ $paciente->id }})">
                                        Editar
                                    </button>
                                    <button type="button" class="btn-acao-remover" onclick="if(confirm('Tem certeza que deseja remover este paciente?')) { window.location.href='{{ route('pacientes.deletar', $paciente->id) }}'; }">
                                        Remover
                                    </button>   
                                @endif
                            </div>
                        </td>
                    </tr>

                    @if(isset($paciente->id))
                    <tr id="form-row-${{ $paciente->id }}" class="form-row-expanded" style="display: none; background-color: #f8fafc;">
                        <td colspan="7" style="padding: 20px; border-bottom: 1px solid #cbd5e1; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.06);">
                            <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <h4 style="margin-top: 0; margin-bottom: 16px; color: #334155;">Editar Cadastro do Paciente</h4>
                                <form action="{{ route('pacientes.salvar') }}" method="POST">
                                    @csrf 
                                    <input type="hidden" name="id" value="{{ $paciente->id }}">

                                    <div style="margin-bottom: 12px;">
                                        <label for="nome-{{ $paciente->id }}">Nome Completo:</label>
                                        <input type="text" id="nome-{{ $paciente->id }}" name="nome" value="{{ $paciente->nome ?? '' }}" required>
                                    </div>

                                    <div class="form-grid-3">
                                        <div>
                                            <label for="cpf-{{ $paciente->id }}">CPF:</label>
                                            <input type="text" id="cpf-{{ $paciente->id }}" name="cpf" class="mascara-cpf" value="{{ $paciente->cpf ?? '' }}" placeholder="000.000.000-00" required>
                                        </div>
                                        <div>
                                            <label for="protocolo-{{ $paciente->id }}">Protocolo:</label>
                                            <input type="text" id="protocolo-{{ $paciente->id }}" name="protocolo" class="mascara-protocolo" value="{{ $paciente->protocolo ?? '' }}" placeholder="#000000000" required>
                                        </div>
                                        <div>
                                            <label for="telefone-{{ $paciente->id }}">Telefone:</label>
                                            <input type="tel" id="telefone-{{ $paciente->id }}" name="telefone" class="mascara-telefone" value="{{ $paciente->telefone ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-grid-3">
                                        <div>
                                            <label for="data_nascimento-{{ $paciente->id }}">Nascimento:</label>
                                            <input type="date" id="data_nascimento-{{ $paciente->id }}" name="data_nascimento" value="{{ $paciente->data_nascimento ?? '' }}">
                                        </div>
                                        <div>
                                            <label for="idade-{{ $paciente->id }}">Idade:</label>
                                            <input type="number" id="idade-{{ $paciente->id }}" name="idade" value="{{ $paciente->idade ?? '' }}">
                                        </div>
                                        <div>
                                            <label for="sexo-{{ $paciente->id }}">Sexo:</label>
                                            <select id="sexo-{{ $paciente->id }}" name="sexo">
                                                <option value="">Selecione...</option>
                                                <option value="M" {{ ($paciente->sexo ?? '') == 'M' ? 'selected' : '' }}>M</option>
                                                <option value="F" {{ ($paciente->sexo ?? '') == 'F' ? 'selected' : '' }}>F</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-grid-3">
                                        <div>
                                            <label for="temperatura-{{ $paciente->id }}">Temperatura:</label>
                                            <input type="number" id="temperatura-{{ $paciente->id }}" name="temperatura" value="{{ $paciente->temperatura ?? '' }}" step="0.1">
                                        </div>
                                        <div>
                                            <label for="bpm-{{ $paciente->id }}">BPM:</label>
                                            <input type="number" id="bpm-{{ $paciente->id }}" name="bpm" value="{{ $paciente->bpm ?? '' }}" step="1">
                                        </div>
                                        <div>
                                            <label for="oxigenacao-{{ $paciente->id }}">Oxigenação:</label>
                                            <input type="number" id="oxigenacao-{{ $paciente->id }}" name="oxigenacao" value="{{ $paciente->oxigenacao ?? '' }}" step="1">
                                        </div>
                                    </div>

                                    <div class="form-grid-2">
                                        <div>
                                            <label for="tipo_sanguineo-{{ $paciente->id }}">Tipo Sanguíneo:</label>
                                            <select id="tipo_sanguineo-{{ $paciente->id }}" name="tipo_sanguineo">
                                                <option value="">...</option>
                                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                                                    <option value="{{ $tipo }}" {{ ($paciente->tipo_sanguineo ?? '') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="urgencia-{{ $paciente->id }}">Classificação (Urgência):</label>
                                            <select id="urgencia-{{ $paciente->id }}" name="urgencia">
                                                <option value="baixa" {{ $urgencia == 'baixa' ? 'selected' : '' }}>Pouco Urgente (Verde)</option>
                                                <option value="media" {{ $urgencia == 'media' ? 'selected' : '' }}>Urgente (Amarelo)</option>
                                                <option value="alta" {{ $urgencia == 'alta' ? 'selected' : '' }}>Emergência (Vermelho)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-grid-2">
                                        <div>
                                            <label for="sintoma-{{ $paciente->id }}">Sintomas:</label>
                                            <textarea id="sintoma-{{ $paciente->id }}" name="sintoma">{{ $paciente->sintomas ?? $paciente->sintoma ?? '' }}</textarea>
                                        </div>
                                        <div>
                                            <label for="antecedentes_pessoais-{{ $paciente->id }}">Antecedentes:</label>
                                            <textarea id="antecedentes_pessoais-{{ $paciente->id }}" name="antecedentes_pessoais">{{ $paciente->antecedentes_pessoais ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="button" class="btn-cancelar" onclick="toggleForm({{ $paciente->id }})">Cancelar</button>
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
        // Correção no seletor de escape para o caractere cifrão, garantindo consistência
        const row = document.getElementById(`form-row-${id}`);
        if(row) {
            row.style.display = row.style.display === "none" ? "table-row" : "none";
        }
    }

    function filtrarTabela() {
        const input = document.getElementById("inputBusca");
        const filter = input.value.toUpperCase();
        const tr = document.querySelectorAll("#tabelaPacientes tbody tr:not(.form-row-expanded)");

        tr.forEach(row => {
            const texto = row.textContent.toUpperCase();
            const proximaLinhaForm = row.nextElementSibling;
            
            if (texto.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
                if (proximaLinhaForm && proximaLinhaForm.classList.contains('form-row-expanded')) {
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
                IMask(input, { mask: '#000000000' });
                input.dataset.maskApplied = true;
            }
        }); 
    };

    document.addEventListener("DOMContentLoaded", aplicarMascaras);
</script>
@endsection