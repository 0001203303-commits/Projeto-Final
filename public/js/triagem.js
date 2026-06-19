document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, "");
            if (v.length > 11) v = v.substring(0, 11);
            v = v.replace(/(\d{3})(\d)/, "$1.$2").replace(/(\d{3})(\d)/, "$1.$2").replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            e.target.value = v;
        });
    }
});

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

let selectedSubcategories = [];
let questionsToAsk = [];
let currentQuestionIndex = 0;
let currentScore = 0;
let userPainLevel = 0;

function goToPage(pageId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
}

function irParaColeta(event) {
    event.preventDefault();
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
    await realizarCarregamento(3000, "#3182ce");

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

    dadosTriagem.bpm = Math.floor(Math.random() * (110 - 65 + 1)) + 65;
    dadosTriagem.spo2 = Math.floor(Math.random() * (100 - 94 + 1)) + 94;
    dadosTriagem.temp = (Math.random() * (38.5 - 36.2) + 36.2).toFixed(1);

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

function renderCategories() {
    selectedSubcategories = [];
    questionsToAsk = [];
    currentQuestionIndex = 0;
    currentScore = 0;
    userPainLevel = 0;
    dadosTriagem.respostas_quiz = [];

    const container = document.getElementById('quiz-options');
    const titulo = document.getElementById('quiz-question');
    
    container.style.display = "grid";
    titulo.innerText = "Olá! Para iniciarmos sua triagem, escolha o grupo que melhor descreve sua queixa:";
    container.innerHTML = "";
    
    window.quizData.categorias.forEach(cat => {
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

    const subs = window.quizData.subcategorias[catId] || [];
    
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
                checkbox.checked = !checkbox.checked;
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
    
    titulo.innerText = "Em uma escala de 0 a 10, qual é o nível da dor ou desconforto que você sente agora?";
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
        dadosTriagem.nivel_dor = userPainLevel;

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
        if (window.quizData.perguntas[subId]) {
            window.quizData.perguntas[subId].forEach(p => {
                // Injetando explicitamente a propriedade stopOnPositive no array de perguntas a fazer
                questionsToAsk.push({ 
                    subcategoria: subId, 
                    texto: p.text, 
                    score: p.score, 
                    stopOnPositive: p.stopOnPositive || false 
                });
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
            dadosTriagem.respostas_quiz.push({ pergunta: perguntaAtual.texto, resposta: 'Sim' });
            
            // SE FOR UMA PARADA CRÍTICA POSITIVA (ALERTA VERMELHO / LARANJA MÁXIMO), ENCERRA IMEDIATAMENTE
            if (perguntaAtual.stopOnPositive) {
                finalizarTriagem();
            } else {
                currentQuestionIndex++;
                askNextQuestion();
            }
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

async function finalizarTriagem() {
    dadosTriagem.score_final = currentScore;

    const titulo = document.getElementById('quiz-question');
    const container = document.getElementById('quiz-options');
    
    titulo.innerText = "Processando Classificação de Risco...";
    container.innerHTML = `<div style="text-align:center; width:100%; color:var(--primary);"><i data-lucide="loader" class="animate-spin" size="48"></i> Enviando dados para o sistema...</div>`;
    lucide.createIcons();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const rotaSalvar = document.querySelector('meta[name="route-salvar"]').content;

    try {
        const resposta = await fetch(rotaSalvar, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify(dadosTriagem)
        });

        const resultadoServidor = await resposta.json();

        if (resultadoServidor.success) {
            titulo.innerText = "Triagem Concluída com Sucesso!";
            container.style.display = "block";
            container.innerHTML = `
                <p style="text-align:center; font-size:18px; margin-bottom:20px;">
                    Paciente: <strong>${dadosTriagem.nome}</strong><br>
                    Sua classificação foi calculada com sucesso pelo Protocolo de Manchester.
                </p>
                <div class="result-badge" style="background-color: ${resultadoServidor.cor_hex}; text-align: center; padding: 15px; border-radius: 8px; color: white; font-weight: bold;">
                    Classificação: ${resultadoServidor.classificacao}
                </div>
                <button class="btn-main" onclick="window.location.reload()" style="margin-top:20px; width: 100%;">
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
            <button class="btn-main" onclick="finalizarTriagem()" style="margin-top:20px; width: 100%;">TENTAR NOVAMENTE</button>
        `;
        lucide.createIcons();   
    }
}

window.goToPage = goToPage;
window.irParaColeta = irParaColeta;
window.finalizarTriagem = finalizarTriagem;