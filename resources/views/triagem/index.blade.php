<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART TRIAGE | Atendimento</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #069494;
            --primary-dark: #046c6c;
            --primary-light: #e6f5f5;
            --accent: #10b981; 
            --bg-light: #f1f7f7;
            --text-main: #1e293b;
            --white: #ffffff;
            --gray-muted: #64748b;
            --border-color: #e2eef0;
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }

        body { 
            background-color: var(--bg-light); 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden;
            color: var(--text-main);
        }

        .page {
            display: none;
            flex-direction: column;
            height: 100%;
            animation: fadeIn 0.4s ease-out forwards
        }

        .page.active { display: flex; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- CABEÇALHO --- */
        .header-teal {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 35px 20px;
            text-align: center;
            border-radius: 0 0 40px 40px;
            box-shadow: 0 10px 25px rgba(6, 148, 148, 0.15);
        }

        .header-teal h1 {
            font-size: 28px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- WELCOME --- */
        .welcome-bg {
            background: radial-gradient(circle at top right, #ffffff, #e6f5f5);
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-card {
            text-align: center;
            width: 90%;
            max-width: 800px; 
            background: white;
            padding: 65px 50px; 
            border-radius: 40px;
            box-shadow: 0 35px 60px -15px rgba(6, 148, 148, 0.15);
        }

        .welcome-card h2 { font-size: 44px; font-weight: 800; color: var(--primary-dark); margin-bottom: 20px; }
        .welcome-card p { font-size: 18px; color: var(--gray-muted); line-height: 1.6; margin-bottom: 45px; max-width: 550px; margin-left: auto; margin-right: auto; }

        /* --- CARDS E FORMS --- */
        .form-container { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
            overflow-y: auto;
        }
        
        .card-standard {
            background: white;
            width: 100%;
            max-width: 550px;
            padding: 40px;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            border: 1px solid var(--border-color);
        }

        /* --- BARRA DE CARREGAMENTO --- */
        .loader-container {
            width: 100%;
            height: 14px;
            background: #e2eef0;
            border-radius: 20px;
            margin: 25px 0;
            overflow: hidden;
        }

        .loader-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 20px;
            transition: width 0.1s linear, background 0.4s ease;
        }

        /* --- GRID DE RESULTADOS DOS VITAIS --- */
        .vital-grid {
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 25px;
            animation: fadeIn 0.6s ease-out forwards;
        }

        .vital-card {
            padding: 22px 12px;
            border-radius: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .vital-value { font-size: 26px; font-weight: 800; }
        .vital-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* --- BOTÕES --- */
        .btn-main {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 20px;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            pointer-events: auto !important;
            user-select: auto !important;
        }

        .btn-main:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 148, 148, 0.2);
        }

        .btn-main:active { transform: translateY(0); }

        .input-group { margin-bottom: 25px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--primary-dark); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        input { 
            width: 100%; 
            padding: 16px; 
            border-radius: 14px; 
            border: 2px solid var(--border-color); 
            font-size: 16px; 
            background-color: #f9fdfd; 
            color: var(--text-main);
        }
        input:focus { outline: none; border-color: var(--primary); background-color: var(--white); }

        /* --- SEÇÃO DO QUESTIONÁRIO --- */
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-top: 10px;
        }

        .btn-quiz {
            padding: 20px;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            background: white;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
            font-size: 15px;
            color: var(--text-main);
        }

        .btn-quiz:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: scale(1.02);
        }

        /* Bordas de Categorias */
        .cat-blue { border-left: 8px solid #3b82f6; }
        .cat-red { border-left: 8px solid #ef4444; }
        .cat-orange { border-left: 8px solid #f97316; }
        .cat-yellow { border-left: 8px solid #eab308; }
        .cat-purple { border-left: 8px solid #a855f7; }
        .cat-pink { border-left: 8px solid #ec4899; }

        .result-badge {
            padding: 20px;
            border-radius: 16px;
            color: white;
            font-size: 22px;
            font-weight: 800;
            margin: 25px 0;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.5px;
        }

        @media print {
            body { background: white; overflow: visible; }
            .page { display: none !important; }
            #page-quiz { display: flex !important; }
            .header-teal, .btn-main, #quiz-question, .btn-quiz { display: none !important; }
            .card-standard { border: none; box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

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

    <div id="page-cadastro" class="page">
        <header class="header-teal">
            <h1><i data-lucide="user-plus"></i> IDENTIFICAÇÃO</h1>
        </header>
        <main class="form-container">
            <div class="card-standard">
                <form class="list" method="post" action="{{ route('pacientes.salvar') }}" onsubmit="irParaColeta(event)">
                    @csrf
                    <div class="input-group">
                        <label for="nome">Nome Completo</label>
                        <input value="{{ $p->nome ?? '' }}" id="nome" name="nome" placeholder="Insira seu nome" required autocomplete="name">
                    </div>
                    <div class="input-group">
                        <label for="cpf">CPF</label>
                        <input value="{{ $p->cpf ?? '' }}" type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required inputmode="numeric">
                    </div>
                    <button type="submit" class="btn-main">PRÓXIMO PASSO <i data-lucide="chevron-right"></i></button>
                </form>
            </div>
        </main>
    </div>

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

                <div class="vital-grid" id="final-results" style="display: none;">
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

    <script>
    lucide.createIcons();

    // Objeto global para armazenar todos os dados recolhidos na sessão
    const dadosTriagem = {
        nome: '',
        cpf: '',
        bpm: 0,
        spo2: 0,
        temp: 0.0,
        nivel_dor: 0,
        respostas_quiz: [],
        score_final: 0
    };

    function goToPage(pageId) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById(pageId).classList.add('active');
    }

    document.getElementById('cpf').addEventListener('input', (e) => {
        let v = e.target.value.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        v = v.replace(/(\d{3})(\d)/, "$1.$2").replace(/(\d{3})(\d)/, "$1.$2").replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        e.target.value = v;
    });

    function irParaColeta(event) {
        event.preventDefault();
        
        // 1. Salva os dados de identificação digitados
        dadosTriagem.nome = document.getElementById('nome').value;
        dadosTriagem.cpf = document.getElementById('cpf').value;

        goToPage('page-vitals');
        iniciarFluxoColeta();
    }

    function realizarCarregamento(tempoMs, cor) {
        return new Promise(resolve => {
            const fill = document.getElementById('loader-fill');
            fill.style.background = cor;
            fill.style.width = "0%";
            
            let start = null;
            function step(timestamp) {
                if (!start) start = timestamp;
                let progress = timestamp - start;
                let percent = Math.min((progress / tempoMs) * 100, 100);
                fill.style.width = percent + "%";
                if (progress < tempoMs) {
                    window.requestAnimationFrame(step);
                } else {
                    setTimeout(resolve, 200);
                }
            }
            window.requestAnimationFrame(step);
        });
    }

    async function iniciarFluxoColeta() {
        const text = document.getElementById('instruction-text');
        const sub = document.getElementById('sub-instruction');
        const icon = document.getElementById('instruction-icon');

        icon.innerHTML = '<i data-lucide="fingerprint" size="80"></i>';
        icon.style.color = "#3182ce";
        text.innerText = "PASSO 1: Oximetria";
        sub.innerText = "Insira seu dedo indicador firmemente no oxímetro acoplado ao totem e mantenha-o imóvel.";
        lucide.createIcons();
        await realizarCarregamento(3000, "#3182ce"); // Reduzi para 3s para testar mais rápido

        icon.innerHTML = '<i data-lucide="thermometer" size="80"></i>';
        icon.style.color = "#dd6b20";
        text.innerText = "PASSO 2: Temperatura Corporal";
        sub.innerText = "Aproxime sua testa ou o seu pulso do sensor infravermelho lateral (mantenha a distância de 5cm).";
        lucide.createIcons();
        await realizarCarregamento(3000, "#dd6b20");

        icon.innerHTML = '<i data-lucide="refresh-cw" size="80"></i>';
        icon.style.color = "var(--primary)";
        text.innerText = "Analisando Sinais Vitais...";
        sub.innerText = "Aguarde enquanto estruturamos os dados aferidos.";
        lucide.createIcons();
        await realizarCarregamento(1500, "var(--accent)");

        // 2. Simulando ou gerando dados dinâmicos para os vitais antes de exibir
        dadosTriagem.bpm = Math.floor(Math.random() * (110 - 65 + 1)) + 65;
        dadosTriagem.spo2 = Math.floor(Math.random() * (100 - 94 + 1)) + 94;
        dadosTriagem.temp = (Math.random() * (38.5 - 36.2) + 36.2).toFixed(1);

        // Atualiza a tela com os valores gerados
        document.getElementById('vital-bpm').innerText = dadosTriagem.bpm;
        document.getElementById('vital-spo2').innerText = dadosTriagem.spo2 + "%";
        document.getElementById('vital-temp').innerText = dadosTriagem.temp + "°C";

        document.getElementById('instruction-step').style.display = "none";
        document.getElementById('loader-bar').style.display = "none";
        document.getElementById('final-results').style.display = "grid";
        
        const btnConcluir = document.getElementById('btn-concluir');
        btnConcluir.style.display = "flex";
        btnConcluir.onclick = () => {
            goToPage('page-quiz');
            renderCategories();
        };
        
        document.getElementById('vitals-title').innerHTML = '<i data-lucide="check-circle"></i> SINAIS VITAIS COLETADOS';
        lucide.createIcons();
    }

    // [Mantido as estruturas de quizData do seu código original]
    const quizData = {
        categorias: [
            { id: 'resp', label: 'Problemas Respiratórios', icon: 'wind', class: 'cat-blue' },
            { id: 'dor', label: 'Dores pelo Corpo', icon: 'alert-circle', class: 'cat-red' },
            { id: 'trauma', label: 'Acidentes e Machucados', icon: 'shield-alert', class: 'cat-orange' },
            { id: 'malestar', label: 'Mal-estar ou Febre', icon: 'thermometer', class: 'cat-yellow' },
            { id: 'neuro', label: 'Alterações Oculares ou de Força', icon: 'brain', class: 'cat-purple' },
            { id: 'gestante', label: 'Gestantes', icon: 'heart', class: 'cat-pink' }
        ],
        subcategorias: {
            resp: [
                { id: 'Falta de Ar', label: 'Falta de ar ou cansaço para respirar', icon: 'alert-triangle' },
                { id: 'Asma / Chiado', label: 'Chiado no peito ou crise de asma', icon: 'activity' },
                { id: 'Tosse / Gripe', label: 'Tosse persistente ou sintomas gripais', icon: 'file-text' }
            ],
            dor: [
                { id: 'Dor no Peito', label: 'Dor ou forte aperto no peito', icon: 'heart' },
                { id: 'Dor de Cabeça', label: 'Dor de cabeça intensa', icon: 'frown' },
                { id: 'Dor Abdominal', label: 'Dor na barriga ou abdômen', icon: 'activity' }
            ],
            trauma: [
                { id: 'Quedas', label: 'Quedas, batidas ou pancadas', icon: 'accessibility' },
                { id: 'Fraturas', label: 'Suspeita de fratura ou torção nas articulações', icon: 'bone' },
                { id: 'Cortes', label: 'Cortes ou pequenos ferimentos com sangue', icon: 'droplet' }
            ],
            malestar: [
                { id: 'Febre', label: 'Febre alta ou calafrios incômodos', icon: 'thermometer' },
                { id: 'Vômitos', label: 'Vômitos constantes ou diarreia', icon: 'shield-alert' },
                { id: 'Tontura', label: 'Tontura, fraqueza ou sensação de desmaio', icon: 'orbit' }
            ],
            neuro: [
                { id: 'AVC / Derrame', label: 'Formigamento ou perda de força súbita de um lado do corpo', icon: 'zap' }
            ],
            gestante: [
                { id: 'Dores / Contrações', label: 'Contrações ou dores no final da gestação', icon: 'clock' },
                { id: 'Sintomas Visuais', label: 'Visão embaçada ou com pontos brilhantes', icon: 'eye' }
            ]
        },
       perguntas: {
            'Falta de Ar': [
                { text: "Você sente dificuldade para completar uma frase curta de forma falada por falta de ar?", score: 11, stopOnPositive: true },
                { text: "Sua boca ou as pontas dos dedos estão ficando com uma cor azulada ou arroxeada?", score: 11, stopOnPositive: true },
                { text: "Esse cansaço ou falta de ar está presente mesmo quando você está parado?", score: 8, stopOnPositive: false },
                { text: "Essa falta de ar começou de maneira repentina e está piorando continuamente?", score: 5, stopOnPositive: false },
                { text: "A falta de ar é leve, mas vem acompanhada de tosse e coriza há alguns dias?", score: 3, stopOnPositive: false }
            ],
            'Asma / Chiado': [
                { text: "Você está tão exausto pelo esforço para respirar que mal consegue responder a esta pergunta?", score: 11, stopOnPositive: true },
                { text: "Seu peito começou a chiar de maneira muito rápida e agressiva nas últimas horas?", score: 8, stopOnPositive: false },
                { text: "Você chegou a usar sua medicação ou bombinha em casa e mesmo assim o chiado não melhorou?", score: 5, stopOnPositive: false },
                { text: "O chiado é leve e costuma aparecer apenas quando você faz algum esforço físico?", score: 3, stopOnPositive: false }
            ],
            'Tosse / Gripe': [
                { text: "Você está tossindo sangue vivo em quantidade preocupante?", score: 11, stopOnPositive: true },
                { text: "Você percebe que sua respiração está muito mais rápida do que o seu normal?", score: 8, stopOnPositive: false },
                { text: "Você pertence a algum grupo prioritário ou de risco (idoso, asmático crônico ou cardiopata)?", score: 5, stopOnPositive: false },
                { text: "Sua tosse é seca ou com catarro claro, sem apresentar febre ou falta de ar?", score: 3, stopOnPositive: false }
            ],
            'Dor no Peito': [
                { text: "A sua dor no peito parece um aperto ou peso que se espalha para o braço esquerdo, queixo ou costas?", score: 11, stopOnPositive: true },
                { text: "Você percebeu a presença de suor frio ou forte tontura acompanhando essa dor no peito?", score: 11, stopOnPositive: true },
                { text: "A dor piora muito profundamente quando você respira fundo ou quando aperta o osso do peito?", score: 5, stopOnPositive: false },
                { text: "É uma dor em pontada leve que dura poucos segundos e desaparece sozinha?", score: 3, stopOnPositive: false }
            ],
            'Dor de Cabeça': [
                { text: "Esta dor de cabeça começou de maneira repentina e é a pior dor que você já sentiu na vida?", score: 11, stopOnPositive: true },
                { text: "Você sente uma rigidez ou dor incômoda na nuca ao tentar aproximar o queixo do peito?", score: 11, stopOnPositive: true },
                { text: "A dor de cabeça veio acompanhada de febre alta ou forte sensibilidade à luz (fotofobia)?", score: 8, stopOnPositive: false },
                { text: "Essa dor é crônica (recorrente), parecida com crises anteriores de enxaqueca?", score: 5, stopOnPositive: false },
                { text: "É uma dor leve, associada a cansaço visual ou estresse do dia a dia?", score: 3, stopOnPositive: false }
            ],
            'Dor Abdominal': [
                { text: "A dor na sua barriga começou de forma súbita, é insuportável e ela parece dura ao toque como uma tábua?", score: 11, stopOnPositive: true },
                { text: "A dor na sua barriga está tão intensa que impede você de caminhar com o corpo ereto?", score: 8, stopOnPositive: false },
                { text: "Ao tocar na sua barriga, você sente que a região está visivelmente endurecida ou muito dolorida?", score: 5, stopOnPositive: false },
                { text: "A dor abdominal é acompanhada de febre baixa ou náuseas leves?", score: 5, stopOnPositive: false },
                { text: "Trata-se de uma cólica leve ou desconforto após a ingestão de algum alimento específico?", score: 3, stopOnPositive: false }
            ],
            'Quedas': [
                { text: "Após a queda, você desmaiou, ficou inconsciente por algum tempo ou teve uma convulsão?", score: 11, stopOnPositive: true },
                { text: "Você bateu a cabeça e passou a sentir enjoo persistente ou tontura após o impacto?", score: 8, stopOnPositive: false },
                { text: "Houve sangramento no couro cabeludo que já parou após fazer pressão?", score: 5, stopOnPositive: false },
                { text: "A queda causou apenas um hematoma ('galo') ou dor leve local, sem outros sintomas?", score: 3, stopOnPositive: false }
            ],
            'Fraturas': [
                { text: "Existe algum osso exposto (fratura exposta) ou o membro machucado está sem pulso, frio ou ficando roxo?", score: 11, stopOnPositive: true },
                { text: "O local machucado apresenta algum desalinhamento visível ou incapacidade total de movimento?", score: 8, stopOnPositive: false },
                { text: "O local está muito inchado e dolorido, mas você ainda consegue mexer os dedos com dificuldade?", score: 5, stopOnPositive: false },
                { text: "Trata-se apenas de uma torção leve, com dor suportável e sem deformidades?", score: 3, stopOnPositive: false }
            ],
            'Cortes': [
                { text: "O ferimento está jorrando sangue de forma pulsante ou não para de sangrar mesmo pressionando com força?", score: 11, stopOnPositive: true },
                { text: "O corte é profundo, você consegue ver tecidos internos (gordura/músculo) e precisa de pontos?", score: 8, stopOnPositive: false },
                { text: "O ferimento continua sangrando de maneira lenta e constante, mesmo após você pressionar o local?", score: 5, stopOnPositive: false },
                { text: "É um arranhão superficial ou corte pequeno que já parou de sangrar por completo?", score: 3, stopOnPositive: false }
            ],
            'Febre': [
                { text: "A febre está acompanhada de confusão mental, sonolência profunda ou manchas vermelhas/roxas pelo corpo?", score: 11, stopOnPositive: true },
                { text: "O paciente é um bebê com menos de 3 meses de vida apresentando febre medida?", score: 8, stopOnPositive: false },
                { text: "Sua febre continua alta mesmo depois de você ter tomado remédios antitérmicos comuns?", score: 5, stopOnPositive: false },
                { text: "Você tem um estado febril leve (abaixo de 38°C) que melhora logo após tomar medicamento?", score: 3, stopOnPositive: false }
            ],
            'Vômitos': [
                { text: "Você está vomitando sangue escuro (parecendo borra de café) ou conteúdo com odor de fezes?", score: 11, stopOnPositive: true },
                { text: "Você apresenta sinais de desidratação severa (boca totalmente seca, muita fraqueza e está sem urinar)?", score: 8, stopOnPositive: false },
                { text: "Você apresentou episódios muito frequentes de vômito e não consegue reter nenhum líquido na barriga?", score: 5, stopOnPositive: false },
                { text: "Teve poucos episódios de vômito ou diarreia, mas consegue se hidratar com soro ou água?", score: 3, stopOnPositive: false }
            ],
            'Tontura': [
                { text: "A tontura surgiu junto com dor no peito, falta de ar ou palpitações fortes no coração?", score: 11, stopOnPositive: true },
                { text: "A tontura fez você desmaiar de verdade ou perder a consciência por alguns segundos?", score: 8, stopOnPositive: false },
                { text: "A tontura causa uma perda real de equilíbrio, impedindo que você caminhe com segurança?", score: 5, stopOnPositive: false },
                { text: "Você sente uma tontura leve apenas quando se levanta rápido demais da cama ou da cadeira?", score: 3, stopOnPositive: false }
            ],
            'AVC / Derrame': [
                { text: "Essa sensação de fraqueza, formigamento ou paralisia começou de forma súbita nas últimas horas?", score: 11, stopOnPositive: true },
                { text: "Você está percebendo alguma dificuldade incomum para pronunciar palavras simples ou compreender os outros neste momento?", score: 11, stopOnPositive: true },
                { text: "Ao sorrir no espelho, você percebeu que um lado do seu rosto está caído ou a boca torta?", score: 11, stopOnPositive: true },
                { text: "Você sente uma perda súbita de coordenação motora ou incapacidade de segurar um objeto leve com uma das mãos?", score: 11, stopOnPositive: true }
            ],
            'Dores / Contrações': [
                { text: "Você está grávida e apresenta sangramento vaginal abundante ou perda de líquido escuro/com odor forte?", score: 11, stopOnPositive: true },
                { text: "Você está com dor de cabeça muito forte, visão borrada e pressão alta nesta fase final da gestação?", score: 11, stopOnPositive: true },
                { text: "Suas contrações estão acontecendo de maneira regular, em intervalos menores do que 5 minutos?", score: 8, stopOnPositive: false },
                { text: "Você sente as famosas contrações de treinamento (esporádicas, irregulares e de baixa intensidade)?", score: 3, stopOnPositive: false }
            ],
            'Sintomas Visuais': [
                { text: "Você sofreu uma perda total e repentina da visão em um ou em ambos os olhos?", score: 11, stopOnPositive: true },
                { text: "Além das alterações na visão, você está sentindo uma dor de cabeça avassaladora?", score: 11, stopOnPositive: true },
                { text: "Você sofreu algum trauma químico direto (respingo de produto de limpeza ou ácido) nos olhos?", score: 8, stopOnPositive: false },
                { text: "Você está percebendo uma visão embaçada, dupla ou com pontos brilhantes de forma persistente?", score: 5, stopOnPositive: false },
                { text: "Seus olhos estão vermelhos, coçando ou lacrimejando, sem alteração na qualidade da visão?", score: 3, stopOnPositive: false }
            ]
        }
    };

    let selectedSubcategories = [];
    let questionsToAsk = [];
    let currentQuestionIndex = 0;
    let currentScore = 0;
    let userPainLevel = 0;

    function renderCategories() {
        selectedSubcategories = [];
        questionsToAsk = [];
        currentQuestionIndex = 0;
        currentScore = 0;
        userPainLevel = 0;

        const container = document.getElementById('quiz-options');
        const titulo = document.getElementById('quiz-question');
        
        container.style.display = "grid";
        titulo.innerText = "Olá! Para iniciarmos sua triagem, escolha o grupo que melhor descreve sua queixa:";
        container.innerHTML = "";
        
        quizData.categorias.forEach(cat => {
            const btn = document.createElement('button');
            btn.className = `btn-quiz ${cat.class}`;
            btn.innerHTML = `<i data-lucide="${cat.icon}"></i> ${cat.label}`;
            btn.onclick = () => renderSubCategories(cat.id, cat.class);
            container.appendChild(btn);
        });
        lucide.createIcons();
    }

    function renderSubCategories(catId, catClass) {
        const container = document.getElementById('quiz-options');
        const titulo = document.getElementById('quiz-question');
        
        titulo.innerText = "Selecione um ou mais sintomas que você está sentindo no momento:";
        container.innerHTML = "";

        const subs = quizData.subcategorias[catId];
        
        subs.forEach(sub => {
            const wrapper = document.createElement('div');
            wrapper.style.display = "flex";
            wrapper.style.alignItems = "center";
            wrapper.style.padding = "18px";
            wrapper.style.background = "#f8fafc";
            wrapper.style.border = "2px solid #e2e8f0";
            wrapper.style.borderRadius = "14px";
            wrapper.style.cursor = "pointer";
            wrapper.style.transition = "all 0.2s";

            wrapper.innerHTML = `
                <input type="checkbox" id="chk-${sub.id}" value="${sub.id}" style="width: 26px; height: 26px; margin-right: 15px; cursor: pointer;">
                <label for="chk-${sub.id}" style="font-size: 18px; font-weight: 600; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 10px; width: 100%;">
                    <i data-lucide="${sub.icon}"></i> ${sub.label}
                </label>
            `;

            const checkbox = wrapper.querySelector('input');
            wrapper.onclick = (e) => {
                if(e.target !== checkbox && e.target.tagName !== 'LABEL') {
                    checkbox.checked = !checkbox.check
                }
                wrapper.style.borderColor = checkbox.checked ? "var(--primary)" : "#e2e8f0";
                wrapper.style.background = checkbox.checked ? "#f0fdf4" : "#f8fafc";
            };

            container.appendChild(wrapper);
        });

        const actions = document.createElement('div');
        actions.style.gridColumn = "1 / -1";
        actions.style.display = "flex";
        actions.style.gap = "15px";
        actions.style.marginTop = "25px";

        const btnVoltar = document.createElement('button');
        btnVoltar.className = "btn-main";
        btnVoltar.style.background = "#64748b";
        btnVoltar.style.flex = "1";
        btnVoltar.innerHTML = `<i data-lucide="arrow-left"></i> Voltar`;
        btnVoltar.onclick = () => renderCategories();

        const btnAvancar = document.createElement('button');
        btnAvancar.className = "btn-main";
        btnAvancar.style.flex = "2";
        btnAvancar.innerHTML = `Continuar <i data-lucide="arrow-right"></i>`;
        btnAvancar.onclick = () => {
            const checked = container.querySelectorAll('input[type="checkbox"]:checked');
            if (checked.length === 0) {
                alert("Por favor, selecione ao menos uma opção antes de prosseguir.");
                return;
            }
            checked.forEach(cb => selectedSubcategories.push(cb.value));
            renderPainScale();
        };

        actions.appendChild(btnVoltar);
        actions.appendChild(btnAvancar);
        container.appendChild(actions);
        lucide.createIcons();
    }

    function renderPainScale() {
        const container = document.getElementById('quiz-options');
        const titulo = document.getElementById('quiz-question');
        
        titulo.innerText = "Em uma escala de 0 a 10, qual é o nível da dor ou desconforto que você siente agora?";
        container.innerHTML = "";
        container.style.display = "block";

        const scaleWrapper = document.createElement('div');
        scaleWrapper.style.width = "100%";
        scaleWrapper.style.textAlign = "center";
        scaleWrapper.style.padding = "20px 0";

        const scoreDisplay = document.createElement('div');
        scoreDisplay.style.fontSize = "64px";
        scoreDisplay.style.fontWeight = "800";
        scoreDisplay.style.color = "#64748b";
        scoreDisplay.style.marginBottom = "10px";
        scoreDisplay.innerText = "0";

        const descDisplay = document.createElement('div');
        descDisplay.style.fontSize = "18px";
        descDisplay.style.fontWeight = "600";
        descDisplay.style.color = "#64748b";
        descDisplay.style.marginBottom = "30px";
        descDisplay.innerText = "Sem Dor";

        const slider = document.createElement('input');
        slider.type = "range";
        slider.min = "0";
        slider.max = "10";
        slider.value = "0";
        slider.style.width = "100%";
        slider.style.height = "25px";
        slider.style.cursor = "pointer";

        const colors = ["#22c55e", "#4ade80", "#a3e635", "#d9f99d", "#fef08a", "#fef08a", "#fed7aa", "#fdba74", "#f97316", "#ef4444", "#b91c1c"];
        const descricoes = ["Sem Dor", "Dor muito leve", "Dor leve", "Dor tolerável", "Dor moderada", "Dor incômoda", "Dor intensa", "Dor muito forte", "Dor severa", "Dor insuportável", "A pior dor possível"];

        slider.oninput = () => {
            const val = parseInt(slider.value);
            scoreDisplay.innerText = val;
            scoreDisplay.style.color = colors[val];
            descDisplay.innerText = descricoes[val];
            descDisplay.style.color = colors[val];
            userPainLevel = val;
        };

        scaleWrapper.appendChild(scoreDisplay);
        scaleWrapper.appendChild(descDisplay);
        scaleWrapper.appendChild(slider);
        container.appendChild(scaleWrapper);

        const btnConfirmarDor = document.createElement('button');
        btnConfirmarDor.className = "btn-main";
        btnConfirmarDor.style.marginTop = "30px";
        btnConfirmarDor.style.width = "100%";
        btnConfirmarDor.innerHTML = `Confirmar e Responder Perguntas <i data-lucide="chevron-right"></i>`;
        btnConfirmarDor.onclick = () => {
            dadosTriagem.nivel_dor = userPainLevel; // Salva o nível da dor

            if (userPainLevel >= 9) currentScore = 11;
            else if (userPainLevel >= 7) currentScore = 8;
            else if (userPainLevel >= 5) currentScore = 5;

            buildQuestionnaire();
        };
        container.appendChild(btnConfirmarDor);
        lucide.createIcons();
    }

    function buildQuestionnaire() {
        questionsToAsk = [];
        selectedSubcategories.forEach(subId => {
            if (quizData.perguntas[subId]) {
                quizData.perguntas[subId].forEach(p => {
                    questionsToAsk.push({ subcategoria: subId, texto: p.text, score: p.score });
                });
            }
        });

        if (questionsToAsk.length > 0) {
            currentQuestionIndex = 0;
            askNextQuestion();
        } else {
            finalizarTriagem();
        }
    }

    function askNextQuestion() {
        const container = document.getElementById('quiz-options');
        const titulo = document.getElementById('quiz-question');
        container.innerHTML = "";
        container.style.display = "grid";
        container.style.gridTemplateColumns = "1fr 1fr";
        container.style.gap = "20px";

        if (currentQuestionIndex < questionsToAsk.length) {
            const perguntaAtual = questionsToAsk[currentQuestionIndex];
            titulo.innerText = perguntaAtual.texto;

            const btnSim = document.createElement('button');
            btnSim.className = "btn-quiz cat-red";
            btnSim.style.justifyContent = "center";
            btnSim.innerHTML = `<i data-lucide="check"></i> SIM`;
            btnSim.onclick = () => {
                if (perguntaAtual.score > currentScore) {
                    currentScore = perguntaAtual.score;
                }
                // Registra resposta positiva
                dadosTriagem.respostas_quiz.push({ pergunta: perguntaAtual.texto, resposta: 'Sim' });
                currentQuestionIndex++;
                askNextQuestion();
            };

            const btnNao = document.createElement('button');
            btnNao.className = "btn-quiz cat-blue";
            btnNao.style.justifyContent = "center";
            btnNao.innerHTML = `<i data-lucide="x"></i> NÃO`;
            btnNao.onclick = () => {
                dadosTriagem.respostas_quiz.push({ pergunta: perguntaAtual.texto, resposta: 'Não' });
                currentQuestionIndex++;
                askNextQuestion();
            };

            container.appendChild(btnSim);
            container.appendChild(btnNao);
            lucide.createIcons();
        } else {
            finalizarTriagem();
        }
    }

    // 3. FUNÇÃO CRUCIAL: Agrupa tudo e envia em tempo real para o Laravel via AJAX
    async function finalizarTriagem() {
        dadosTriagem.score_final = currentScore;

        const titulo = document.getElementById('quiz-question');
        const container = document.getElementById('quiz-options');
        
        titulo.innerText = "Processando Classificação de Risco...";
        container.innerHTML = `<div style="text-align:center; width:100%; color:var(--primary);"><i data-lucide="loader" class="animate-spin" size="48"></i> Enviando dados para o sistema...</div>`;
        lucide.createIcons();

        try {
            // Realiza uma requisição POST assíncrona para a rota do Laravel
            const resposta = await fetch("{{ route('pacientes.salvar') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    // Captura o token CSRF obrigatório do Laravel inserido na sua Blade
                    "X-CSRF-TOKEN": document.querySelector('input[name="_csrf"]')?.value || '{{ csrf_token() }}'
                },
                body: JSON.stringify(dadosTriagem)
            });

            const resultadoServidor = await resposta.json();

            if (resultadoServidor.success) {
                // Renderiza o resultado final baseado no retorno real do servidor
                titulo.innerText = "Triagem Concluída com Sucesso!";
                container.style.display = "block";
                container.innerHTML = `
                    <p style="text-align:center; font-size:18px; margin-bottom:20px;">
                        Paciente: <strong>${dadosTriagem.nome}</strong><br>
                        Sua classificação foi calculada com sucesso pelo Protocolo de Manchester.
                    </p>
                    <div class="result-badge" style="background-color: ${resultadoServidor.cor_hex};">
                        Classificação: ${resultadoServidor.classificacao}
                    </div>
                    <button class="btn-main" onclick="window.location.reload()" style="margin-top:20px;">
                        NOVO ATENDIMENTO <i data-lucide="refresh-cw"></i>
                    </button>
                `;
                lucide.createIcons();
            } else {
                throw new Error(resultadoServidor.message || "Erro desconhecido");
            }

        } catch (erro) {
            titulo.innerText = "Ops! Algo deu errado.";
            container.innerHTML = `
                <p style="color:red; text-align:center;">Não foi possível salvar a triagem no servidor. Código do erro: ${erro.message}</p>
                <button class="btn-main" onclick="finalizarTriagem()" style="margin-top:20px;">TENTAR NOVAMENTE</button>
            `;
        }
    }
</script>
</body>
</html>