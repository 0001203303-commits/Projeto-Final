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
    perguntas : {

'Falta de Ar': [
    {
        text: "Você está sentindo falta de ar mesmo parado(a) ou falando normalmente?",
        score: 10,
        stopOnPositive: true
    },
    {
        text: "A falta de ar começou hoje e está piorando rapidamente?",
        score: 8,
        stopOnPositive: false
    },
    {
        text: "Você percebe dificuldade para respirar ao caminhar pequenas distâncias?",
        score: 6,
        stopOnPositive: false
    },
    {
        text: "Está com tosse, nariz entupido ou sintomas gripais junto com a falta de ar?",
        score: 4,
        stopOnPositive: false
    },
    {
        text: "A sensação é leve e aparece somente em esforços maiores?",
        score: 2,
        stopOnPositive: false
    }
],

'Asma / Chiado': [
    {
        text: "Você está com dificuldade para falar frases completas por causa da respiração?",
        score: 10,
        stopOnPositive: true
    },
    {
        text: "Usou sua medicação habitual e não percebeu melhora?",
        score: 8,
        stopOnPositive: false
    },
    {
        text: "O chiado está atrapalhando dormir ou fazer atividades normais?",
        score: 6,
        stopOnPositive: false
    },
    {
        text: "Está apenas com chiado leve e conseguindo respirar normalmente?",
        score: 2,
        stopOnPositive: false
    }
],

'Tosse / Sintomas Respiratórios': [
    {
        text: "Sua tosse está acompanhada de dificuldade para respirar ou dor ao respirar?",
        score: 8,
        stopOnPositive: true
    },
    {
        text: "Está eliminando catarro em grande quantidade?",
        score: 6,
        stopOnPositive: false
    },
    {
        text: "A tosse está atrapalhando dormir ou atividades normais?",
        score: 4,
        stopOnPositive: false
    },
    {
        text: "São sintomas leves de gripe ou resfriado?",
        score: 2,
        stopOnPositive: false
    }
],

'Dor no Peito': [
    {
        text: "A dor começou de repente e veio com falta de ar, suor frio ou mal-estar?",
        score: 10,
        stopOnPositive: true
    },
    {
        text: "A dor parece pressão, aperto ou peso no peito?",
        score: 8,
        stopOnPositive: false
    },
    {
        text: "A dor piora quando movimenta o corpo ou toca no local?",
        score: 5,
        stopOnPositive: false
    },
    {
        text: "É uma dor leve e passageira sem outros sintomas?",
        score: 2,
        stopOnPositive: false
    }
],

'Dor de Cabeça': [
    {
        text: "A dor começou de forma muito intensa ou diferente do habitual?",
        score: 9,
        stopOnPositive: true
    },
    {
        text: "Está junto com febre, náusea ou dificuldade para olhar luz?",
        score: 7,
        stopOnPositive: false
    },
    {
        text: "Você já teve dores parecidas anteriormente?",
        score: 4,
        stopOnPositive: false
    },
    {
        text: "A dor é leve e não impede suas atividades?",
        score: 2,
        stopOnPositive: false
    }
],

'Dor Abdominal': [
    {
        text: "A dor está muito forte e impede andar ou ficar em pé normalmente?",
        score: 9,
        stopOnPositive: true
    },
    {
        text: "Está junto com vômitos frequentes ou dificuldade para se alimentar?",
        score: 7,
        stopOnPositive: false
    },
    {
        text: "A dor piora após comer ou se movimentar?",
        score: 5,
        stopOnPositive: false
    },
    {
        text: "É um desconforto leve sem outros sintomas?",
        score: 2,
        stopOnPositive: false
    }
],

'Febre': [
    {
        text: "Está com febre acompanhada de muito cansaço ou dificuldade para respirar?",
        score: 8,
        stopOnPositive: true
    },
    {
        text: "A febre permanece por mais de dois dias?",
        score: 6,
        stopOnPositive: false
    },
    {
        text: "Está conseguindo beber líquidos normalmente?",
        score: 4,
        stopOnPositive: false
    },
    {
        text: "A febre melhora com repouso ou medicação habitual?",
        score: 2,
        stopOnPositive: false
    }
],

'Vômitos': [
    {
        text: "Você está conseguindo beber líquidos sem vomitar?",
        score: 8,
        stopOnPositive: false
    },
    {
        text: "Os vômitos estão acontecendo várias vezes no mesmo dia?",
        score: 6,
        stopOnPositive: false
    },
    {
        text: "Está sentindo boca seca ou urinando menos que o normal?",
        score: 5,
        stopOnPositive: false
    },
    {
        text: "Foi apenas um episódio isolado?",
        score: 2,
        stopOnPositive: false
    }
],

'Tontura': [
    {
        text: "Você sente que pode cair ou desmaiar?",
        score: 8,
        stopOnPositive: true
    },
    {
        text: "A tontura começou junto com palpitação ou falta de ar?",
        score: 8,
        stopOnPositive: false
    },
    {
        text: "A tontura aparece ao levantar rapidamente?",
        score: 3,
        stopOnPositive: false
    }
]

};
window.quizData = quizData;