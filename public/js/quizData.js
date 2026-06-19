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
            { text: "Você sente dificuldade para completar uma frase corta de forma falada por falta de ar?", score: 11, stopOnPositive: true },
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
            { text: "Você está percebendo uma visão embaçada, dupla ou com pontos brilhantes de forma persistentemente?", score: 5, stopOnPositive: false },
            { text: "Seus olhos estão vermelhos, coçando ou lacrimejando, sem alteração na qualidade da visão?", score: 3, stopOnPositive: false }
        ]
    }
};

window.quizData = quizData;