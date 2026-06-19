<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-salvar" content="{{ route('pacientes.salvar') }}">
    <title>SMART TRIAGE | Atendimento</title>
    
    <!-- Fontes Externas -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Folha de Estilo Separada -->
    <link rel="stylesheet" href="{{ asset('css/triagem.css') }}">
</head>
<body>

    <!-- PÁGINA WELCOME -->
    <div id="page-welcome" class="page active">
        <div class="welcome-bg">
            <div class="welcome-card">
                <div style="color: var(--primary); margin-bottom: 24px;"><i data-lucide="activity" size="56"></i></div>
                <h2>Smart Triage</h2>
                <p>Tecnologia avançada para triagem hospitalar autônoma. Atendimento rápido, qualificado e em total conformidade técnica.</p>
                <button class="btn-main" onclick="goToPage('page-cadastro')">
                    INICIAR ATENDIMENTO <i data-lucide="arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- PÁGINA CADASTRO -->
    <div id="page-cadastro" class="page">
        <header class="header-teal">
            <h1><i data-lucide="user-plus"></i> IDENTIFICAÇÃO</h1>
        </header>
        <main class="form-container">
            <div class="card-standard">
                <form class="list" onsubmit="irParaColeta(event)">
                    <div class="input-group">
                        <label for="nome">Nome Completo</label>
                        <input id="nome" name="nome" placeholder="Insira seu nome" required autocomplete="name">
                    </div>
                    <div class="input-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required inputmode="numeric">
                    </div>
                    <button type="submit" class="btn-main">PRÓXIMO PASSO <i data-lucide="chevron-right"></i></button>
                </form>
            </div>
        </main>
    </div>

    <!-- PÁGINA VITALS -->
    <div id="page-vitals" class="page">
        <header class="header-teal">
            <h1 id="vitals-title"><i data-lucide="gauge"></i> TRIAGEM EM ANDAMENTO</h1>
        </header>
        <main class="form-container">
            <div class="card-standard" style="text-align: center; min-height: 460px; display: flex; flex-direction: column; justify-content: center;">
                
                <div id="instruction-step">
                    <div id="instruction-icon" style="margin-bottom: 25px; color: var(--primary);">
                        <i data-lucide="fingerprint" size="80"></i>
                    </div>
                    <h2 id="instruction-text" style="font-size: 26px; color: var(--primary-dark); margin-bottom: 12px;">Aguardando...</h2>
                    <p id="sub-instruction" style="font-size: 16px; color: var(--gray-muted); max-width: 420px; margin: 0 auto 25px; line-height: 1.5;">
                        Estamos preparando os sensores de leitura corporal. Por favor, permaneça imóvel.
                    </p>
                </div>

                <div class="loader-container" id="loader-bar">
                    <div class="loader-fill" id="loader-fill"></div>
                </div>

                <div class="vital-grid" id="final-results">
                    <div class="vital-card" style="background: #fff5f5; border: 2px solid #feb2b2;">
                        <i data-lucide="heart" style="color: #e53e3e;"></i>
                        <div class="vital-value" id="vital-bpm" style="color: #c53030;">--</div>
                        <div class="vital-label" style="color: #c53030;">BPM</div>
                    </div>
                    <div class="vital-card" style="background: #ebf8ff; border: 2px solid #90cdf4;">
                        <i data-lucide="droplets" style="color: #3182ce;"></i>
                        <div class="vital-value" id="vital-spo2" style="color: #2b6cb0;">--%</div>
                        <div class="vital-label" style="color: #2b6cb0;">SpO2</div>
                    </div>
                    <div class="vital-card" style="background: #fffaf0; border: 2px solid #fbd38d;">
                        <i data-lucide="thermometer" style="color: #dd6b20;"></i>
                        <div class="vital-value" id="vital-temp" style="color: #c05621;">--°C</div>
                        <div class="vital-label" style="color: #c05621;">TEMP</div>
                    </div>
                </div>

                <button id="btn-concluir" class="btn-main" style="margin-top: 35px; display: none;">
                    AVANÇAR PARA QUESTÕES <i data-lucide="chevron-right"></i>
                </button>
            </div>
        </main>
    </div>

    <!-- PÁGINA QUIZ -->
    <div id="page-quiz" class="page">
        <header class="header-teal">
            <h1><i data-lucide="clipboard-list"></i> AVALIAÇÃO DE SINTOMAS</h1>
        </header>

        <main class="form-container">
            <div class="card-standard" id="quiz-content" style="max-width: 800px; width: 95%;">
                <h2 id="quiz-question" style="text-align: center; margin-bottom: 30px; color: var(--primary-dark); font-size: 24px; font-weight: 700;">
                    O que você está sentindo?
                </h2>
                <div id="quiz-options" class="quiz-grid"></div>
            </div>
        </main>
    </div>

    <!-- Scripts em Ordem de Execução Escopo -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="{{ asset('js/quizData.js') }}"></script>
    <script src="{{ asset('js/triagem.js') }}"></script>
</body>
</html>