// Gerenciamento de Estado Global Único da Aplicação
const AppState = {
    dadosTriagem: {
        nome: '',
        cpf: '',
        bpm: 0,
        spo2: 0,
        temp: 0.0,
        nivel_dor: 0,
        respostas_quiz: [],
        score_final: 0
    },
    selectedSubcategories: [],
    questionsToAsk: [],
    currentQuestionIndex: 0,
    currentScore: 0,
    userPainLevel: 0
};

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, ""); 
            if (v.length > 11) v = v.substring(0, 11);
            
            if (v.length > 9) {
                v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, "$1.$2.$3-$4");
            } else if (v.length > 6) {
                v = v.replace(/^(\d{3})(\d{3})(\d{1,3})$/, "$1.$2.$3");
            } else if (v.length > 3) {
                v = v.replace(/^(\d{3})(\d{1,3})$/, "$1.$2");
            }
            e.target.value = v;
        });

        cpfInput.addEventListener('keydown', (e) => {
            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
            if (!allowedKeys.includes(e.key) && isNaN(Number(e.key))) {
                e.preventDefault();
            }
        });
    }
});

function goToPage(pageId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
}

function irParaColeta(event) {
    event.preventDefault();
    
    const nomeInput = document.getElementById('nome');
    const cpfInput = document.getElementById('cpf');
    
    const nomeValue = nomeInput.value.trim();
    const cpfValue = cpfInput.value.trim();

    // 1. Validação estrita do Nome
    if (!nomeValue || nomeValue.length < 3) {
        alert(nomeInput.getAttribute('required-message') || "Por favor, insira o seu nome completo antes de avançar.");
        nomeInput.focus();
        return; // Bloqueia o avanço
    }

    // 2. Validação estrita do CPF (apenas números)
    const numerosCpf = cpfValue.replace(/\D/g, "");
    if (!numerosCpf || numerosCpf.length !== 11) {
        alert(cpfInput.getAttribute('required-message') || "CPF inválido ou incompleto! Por favor, insira todos os 11 dígitos.");
        cpfInput.focus();
        return; // Bloqueia o avanço
    }

    // Se passou em todas as validações, grava no estado global
    AppState.dadosTriagem.nome = nomeValue;
    AppState.dadosTriagem.cpf = numerosCpf;

    // Avança para a próxima página
    goToPage('page-vitals');
    simularSensores();
}

function simularSensores() {
    const loaderFill = document.getElementById('loader-fill');
    const instructionText = document.getElementById('instruction-text');
    let progress = 0;

    instructionText.innerText = "Coletando Sinais Vitais...";

    const interval = setInterval(() => {
        progress += 5;
        if (loaderFill) loaderFill.style.width = `${progress}%`;

        if (progress >= 100) {
            clearInterval(interval);
            
            AppState.dadosTriagem.bpm = Math.floor(Math.random() * (95 - 70 + 1)) + 70;
            AppState.dadosTriagem.spo2 = Math.floor(Math.random() * (100 - 96 + 1)) + 96;
            AppState.dadosTriagem.temp = parseFloat((Math.random() * (37.2 - 36.2) + 36.2).toFixed(1));

            document.getElementById('vital-bpm').innerText = AppState.dadosTriagem.bpm;
            document.getElementById('vital-spo2').innerText = `${AppState.dadosTriagem.spo2}%`;
            document.getElementById('vital-temp').innerText = `${AppState.dadosTriagem.temp}°C`;

            instructionText.innerText = "Leitura Concluída com Sucesso!";
            
            const btnConcluir = document.getElementById('btn-concluir');
            btnConcluir.style.display = "inline-flex";
            btnConcluir.onclick = () => {
                goToPage('page-quiz');
                renderCategories(); // Inicializa o quiz adaptado para interseção
            };
        }
    }, 100);
}

// PASSO 1 DO QUIZ: Escolha de múltiplas categorias (Interseção)
function renderCategories() {
    AppState.selectedSubcategories = [];
    AppState.questionsToAsk = [];
    AppState.currentQuestionIndex = 0;
    AppState.currentScore = 0;
    AppState.userPainLevel = 0;
    AppState.dadosTriagem.respostas_quiz = [];

    const container = document.getElementById('quiz-options');
    const titulo = document.getElementById('quiz-question');
    
    container.className = "quiz-grid";
    titulo.innerText = "Escolha um ou mais grupos que descrevem o que está a sentir no momento:";
    container.innerHTML = "";
    
    window.quizData.categorias.forEach(cat => {
        const wrapper = document.createElement('div');
        wrapper.className = `sub-card-checkbox ${cat.class}`; 

        wrapper.innerHTML = `
            <input type="checkbox" id="cat-chk-${cat.id}" value="${cat.id}" style="display:none;">
            <label for="cat-chk-${cat.id}" style="width: 100%; display: flex; align-items: center; gap: 15px; cursor: pointer; margin: 0;">
                <i data-lucide="${cat.icon}"></i> ${cat.label}
            </label>
        `;

        const checkbox = wrapper.querySelector('input');
        
        wrapper.onclick = (e) => {
            if (e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }
            wrapper.classList.toggle('checked', checkbox.checked);
        };

        container.appendChild(wrapper);
    });

    const actionsContainer = document.createElement('div');
    actionsContainer.className = "quiz-actions-container";

    const btnAvancar = document.createElement('button');
    btnAvancar.type = "button";
    btnAvancar.className = "btn-main";
    btnAvancar.style.width = "100%";
    btnAvancar.innerHTML = `CONFIRMAR GRUPOS E VER SINTOMAS <i data-lucide="arrow-right"></i>`;
    btnAvancar.onclick = () => {
        const checked = container.querySelectorAll('input[type="checkbox"]:checked');
        if (checked.length === 0) {
            alert("Por favor, selecione pelo menos um grupo de queixas antes de avançar.");
            return;
        }
        
        const categoriasSelecionadas = Array.from(checked).map(cb => cb.value);
        renderSubCategories(categoriasSelecionadas);
    };

    actionsContainer.appendChild(btnAvancar);
    container.appendChild(actionsContainer);
    lucide.createIcons();
}

// PASSO 2 DO QUIZ: Junta todos os sintomas específicos das categorias escolhidas
function renderSubCategories(categoriasIds) {
    const container = document.getElementById('quiz-options');
    const titulo = document.getElementById('quiz-question');
    
    titulo.innerText = "Agora, selecione todos os sintomas específicos que se aplicam ao seu caso:";
    container.innerHTML = "";

    let subsUnificadas = [];
    categoriasIds.forEach(catId => {
        const subs = window.quizData.subcategorias[catId] || [];
        subsUnificadas = subsUnificadas.concat(subs);
    });
    
    subsUnificadas.forEach(sub => {
        const wrapper = document.createElement('div');
        wrapper.className = "sub-card-checkbox";

        wrapper.innerHTML = `
            <input type="checkbox" id="chk-${sub.id}" value="${sub.id}" style="display:none;">
            <label for="chk-${sub.id}" style="width: 100%; display: flex; align-items: center; gap: 15px; cursor: pointer; margin: 0;">
                <i data-lucide="${sub.icon}"></i> ${sub.label}
            </label>
        `;

        const checkbox = wrapper.querySelector('input');
        
        wrapper.onclick = (e) => {
            if (e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }
            wrapper.classList.toggle('checked', checkbox.checked);
        };

        container.appendChild(wrapper);
    });

    const actionsContainer = document.createElement('div');
    actionsContainer.className = "quiz-actions-container";

    const btnVoltar = document.createElement('button');
    btnVoltar.type = "button";
    btnVoltar.className = "btn-main btn-secondary";
    btnVoltar.innerHTML = `<i data-lucide="arrow-left"></i> Voltar`;
    btnVoltar.onclick = () => renderCategories();

    const btnAvancar = document.createElement('button');
    btnAvancar.type = "button";
    btnAvancar.className = "btn-main btn-flex-2";
    btnAvancar.innerHTML = `CONTINUAR <i data-lucide="arrow-right"></i>`;
    btnAvancar.onclick = () => {
        const checked = container.querySelectorAll('input[type="checkbox"]:checked');
        if (checked.length === 0) {
            alert("Por favor, selecione pelo menos um sintoma para continuar.");
            return;
        }
        
        AppState.selectedSubcategories = Array.from(checked).map(cb => cb.value);
        renderPainScale();
    };

    actionsContainer.appendChild(btnVoltar);
    actionsContainer.appendChild(btnAvancar);
    container.appendChild(actionsContainer);
    lucide.createIcons();
}

// PASSO 3 DO QUIZ: Escala de dor
function renderPainScale() {
    const container = document.getElementById('quiz-options');
    const titulo = document.getElementById('quiz-question');
    
    titulo.innerText = "Numa escala de 0 a 10, qual é o nível da sua dor ou desconforto agora?";
    container.className = ""; 
    container.innerHTML = `
        <div class="pain-scale-container">
            <div id="pain-score" class="pain-score-display level-0">0</div>
            <div id="pain-desc" class="pain-desc-display level-0">Sem Dor</div>
            <input type="range" id="pain-range" class="pain-slider" min="0" max="10" value="0">
        </div>
        <div class="quiz-actions-container">
            <button type="button" class="btn-main" id="btn-iniciar-perguntas" style="width: 100%;">
                INICIAR QUESTIONÁRIO DE RISCO <i data-lucide="play"></i>
            </button>
        </div>
    `;

    const slider = document.getElementById('pain-range');
    const displayScore = document.getElementById('pain-score');
    const displayDesc = document.getElementById('pain-desc');

    const descricoesDor = [
        "Sem Dor", "Dor Muito Leve", "Dor Leve", "Dor Suportável", 
        "Dor Moderada", "Dor Incomoda", "Dor Intensa", "Dor Forte", 
        "Dor Muito Forte", "Dor Insuportável", "Pior Dor Imaginável"
    ];

    slider.oninput = function() {
        const val = this.value;
        AppState.userPainLevel = parseInt(val);
        
        displayScore.innerText = val;
        displayScore.className = `pain-score-display level-${val}`;
        
        displayDesc.innerText = descricoesDor[val];
        displayDesc.className = `pain-desc-display level-${val}`;
    };

    document.getElementById('btn-iniciar-perguntas').onclick = () => {
        AppState.dadosTriagem.nivel_dor = AppState.userPainLevel;
        buildQuestionnaire();
    };
    lucide.createIcons();
}

// PASSO 4: Monta o questionário acumulando as perguntas da interseção dos sintomas
function buildQuestionnaire() {
    AppState.questionsToAsk = [];
    
    AppState.selectedSubcategories.forEach(subId => {
        const perguntasSintoma = window.quizData.perguntas[subId] || [];
        perguntasSintoma.forEach(q => {
            // Evita adicionar perguntas repetidas caso sintomas partilhem a mesma regra
            if (!AppState.questionsToAsk.some(existente => existente.text === q.text)) {
                AppState.questionsToAsk.push({
                    sintoma: subId,
                    text: q.text,
                    score: q.score,
                    stopOnPositive: q.stopOnPositive
                });
            }
        });
    });

    if (AppState.questionsToAsk.length === 0) {
        finalizarTriagem();
    } else {
        askNextQuestion();
    }
}

function askNextQuestion() {
    if (AppState.currentQuestionIndex >= AppState.questionsToAsk.length) {
        finalizarTriagem();
        return;
    }

    const container = document.getElementById('quiz-options');
    const titulo = document.getElementById('quiz-question');
    const perguntaAtual = AppState.questionsToAsk[AppState.currentQuestionIndex];

    titulo.innerText = perguntaAtual.text;
    container.className = "quiz-grid dual-column";
    container.innerHTML = `
        <button type="button" class="sub-card-checkbox" id="btn-resp-sim" style="justify-content: center; font-size: 20px; font-weight: bold; height: 100px;">
            <i data-lucide="check-circle" style="color: #22c55e;"></i> SIM
        </button>
        <button type="button" class="sub-card-checkbox" id="btn-resp-nao" style="justify-content: center; font-size: 20px; font-weight: bold; height: 100px;">
            <i data-lucide="x-circle" style="color: #ef4444;"></i> NÃO
        </button>
    `;

    document.getElementById('btn-resp-sim').onclick = () => processaResposta(true);
    document.getElementById('btn-resp-nao').onclick = () => processaResposta(false);
    lucide.createIcons();
}

function processaResposta(respostaSim) {
    const perguntaAtual = AppState.questionsToAsk[AppState.currentQuestionIndex];
    
    AppState.dadosTriagem.respostas_quiz.push({
        pergunta: perguntaAtual.text,
        resposta: respostaSim ? 'Sim' : 'Não',
        sintoma_relacionado: perguntaAtual.sintoma
    });

    if (respostaSim) {
        if (perguntaAtual.score > AppState.currentScore) {
            AppState.currentScore = perguntaAtual.score;
        }
        if (perguntaAtual.stopOnPositive) {
            AppState.currentScore = Math.max(AppState.currentScore, perguntaAtual.score);
            finalizarTriagem();
            return;
        }
    }

    AppState.currentQuestionIndex++;
    askNextQuestion();
}

async function finalizarTriagem() {
    const container = document.getElementById('quiz-options');
    const titulo = document.getElementById('quiz-question');

    titulo.innerText = "A calcular a Classificação...";
    container.className = "";
    container.innerHTML = `
        <div class="loading-center-container" style="text-align:center; padding: 40px;">
            <i data-lucide="refresh-cw" size="48" class="animate-spin" style="margin-bottom:15px;"></i>
            <p style="font-weight: 600;">A processar os dados de saúde com o servidor...</p>
        </div>
    `;
    lucide.createIcons();

    AppState.dadosTriagem.score_final = AppState.currentScore;

    try {
        const urlSalvar = document.querySelector('meta[name="route-salvar"]').getAttribute('content');
        const tokenCsrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const resposta = await fetch(urlSalvar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': tokenCsrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify(AppState.dadosTriagem)
        });

        if (!resposta.ok) throw new Error(`HTTP error! status: ${resposta.status}`);
        
        const resultadoServidor = await resposta.json();

        if (resultadoServidor.success) {
            titulo.innerText = "Triagem Finalizada!";
            container.innerHTML = `
                <div class="result-block-container" style="text-align: center; padding: 20px;">
                    <p class="result-user-info" style="font-size:16px; margin-bottom: 20px;">
                        Paciente: <strong>${AppState.dadosTriagem.nome}</strong><br>
                        A sua classificação de risco foi calculada com sucesso pelo sistema.
                    </p>
                    <div class="result-badge" style="background-color: ${resultadoServidor.cor_hex || '#64748b'}; padding: 15px; border-radius:8px; color:white; font-weight:bold; font-size:18px; margin-bottom:20px;">
                        CLASSIFICAÇÃO: ${resultadoServidor.classificacao}
                    </div>
                    <button type="button" class="btn-main" onclick="window.location.reload()" style="width: 100%;">
                        NOVO ATENDIMENTO <i data-lucide="refresh-cw"></i>
                    </button>
                </div>
            `;
            lucide.createIcons();
        } else {
            throw new Error(resultadoServidor.message || "Erro desconhecido.");
        }

    } catch (erro) {
        titulo.innerText = "Aviso";
        container.innerHTML = `
            <div class="result-block-container" style="text-align:center; padding:20px;">
                <p class="error-text" style="color:#ef4444; font-weight:600;">Não foi possível guardar a triagem no servidor.</p>
                <p style="color: var(--gray-muted); font-size: 14px; margin-bottom: 20px;">Erro: ${erro.message}</p>
                <button type="button" class="btn-main" onclick="finalizarTriagem()" style="width: 100%;">TENTAR NOVAMENTE <i data-lucide="alert-circle"></i></button>
            </div>
        `;
        lucide.createIcons();
    }
}