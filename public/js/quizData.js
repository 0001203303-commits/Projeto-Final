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
        //categoria problemas respiratórios
       resp: [
            {
            id: 'Falta de Ar',
            label: 'Falta de ar ou cansaço para respirar',
            icon: 'alert-triangle'
            },

            {
            id: 'Asma / Chiado',
            label: 'Chiado no peito ou dificuldade para puxar o ar',
            icon: 'activity'
            },

            {
            id: 'Tosse / Gripe',
            label: 'Tosse persistente ou sintomas gripais',
            icon: 'file-text'
            },

            {
            id: 'Congestão Respiratória',
            label: 'Nariz entupido, catarro ou dificuldade para respirar pelo nariz',
            icon: 'cloud'
            },

            {
            id: 'Dor ao Respirar',
            label: 'Dor ou desconforto ao respirar fundo',
            icon: 'wind'
            },

            {
            id: 'Tosse com Catarro',
            label: 'Tosse com secreção ou catarro',
            icon: 'droplets'
            },

            {
            id: 'Respiração Acelerada',
            label: 'Respiração rápida ou sensação de esforço para respirar',
            icon: 'zap'
            },

            {
            id: 'Rouquidão / Garganta',
            label: 'Dor de garganta ou rouquidão',
            icon: 'mic'
            },

            {
            id: 'Sintomas Alérgicos',
            label: 'Espirros, coceira ou sintomas respiratórios de alergia',
            icon: 'flower'
            },

            {
            id: 'Desconforto Respiratório',
            label: 'Sensação de peito pesado ou dificuldade leve para respirar',
            icon: 'shield-alert'
            }

            ],
        //categoria dores
        dor: [
            {
            id: 'Dor no Peito',
            label: 'Dor ou aperto no peito',
            icon: 'heart'
            },

            {
            id: 'Dor de Cabeça',
            label: 'Dor de cabeça ou pressão na cabeça',
            icon: 'frown'
            },

            {
            id: 'Dor Abdominal',
            label: 'Dor na barriga ou abdômen',
            icon: 'activity'
            },

            {
            id: 'Dor nas Costas',
            label: 'Dor nas costas ou região lombar',
            icon: 'align-center'
            },

            {
            id: 'Dor Muscular',
            label: 'Dor muscular ou sensação de corpo dolorido',
            icon: 'accessibility'
            },

            {
            id: 'Dor nas Articulações',
            label: 'Dor em joelhos, cotovelos ou articulações',
            icon: 'move'
            },

            {
            id: 'Dor no Pescoço',
            label: 'Dor ou rigidez no pescoço',
            icon: 'arrow-up'
            },

            {
            id: 'Dor no Ombro',
            label: 'Dor ao levantar ou movimentar o braço',
            icon: 'move-diagonal'
            },

            {
            id: 'Dor no Ouvido',
            label: 'Dor ou pressão no ouvido',
            icon: 'headphones'
            },

            {
            id: 'Dor de Garganta',
            label: 'Dor ou desconforto para engolir',
            icon: 'message-circle'
            },

            {
            id: 'Dor Facial',
            label: 'Dor no rosto ou região do nariz',
            icon: 'scan-face'
            },

            {
            id: 'Dor Generalizada',
            label: 'Dor em várias partes do corpo',
            icon: 'circle'
            }

            ],

        //categoria Trauma
        trauma: [

            {
            id: 'Quedas',
            label: 'Quedas, batidas ou pancadas',
            icon: 'accessibility'
            },

            {
            id: 'Fraturas',
            label: 'Suspeita de fratura ou torção nas articulações',
            icon: 'bone'
            },

            {
            id: 'Cortes',
            label: 'Cortes ou pequenos ferimentos com sangue',
            icon: 'droplet'
            },

            {
            id: 'Contusão',
            label: 'Dor, inchaço ou hematoma após impacto',
            icon: 'circle-dot'
            },

            {
            id: 'Torção Muscular',
            label: 'Torção ou estiramento muscular',
            icon: 'rotate-cw'
            },

            {
            id: 'Dor em Membro',
            label: 'Dor em braço, perna, mão ou pé após esforço ou trauma',
            icon: 'armchair'
            },

            {
            id: 'Inchaço Articular',
            label: 'Inchaço ou dificuldade para movimentar articulações',
            icon: 'move'
            },

            {
            id: 'Machucado Superficial',
            label: 'Arranhões, escoriações ou feridas leves',
            icon: 'bandage'
            },

            {
            id: 'Dor Lombar Pós Esforço',
            label: 'Dor nas costas após esforço ou movimento',
            icon: 'align-center'
            },

            {
            id: 'Trauma Leve na Cabeça',
            label: 'Batida leve na cabeça sem perda de consciência',
            icon: 'helmet'
            },

            {
            id: 'Dor no Pescoço',
            label: 'Dor ou rigidez após movimento ou impacto leve',
            icon: 'arrow-up'
            },

            {
            id: 'Dor no Ombro',
            label: 'Dor para levantar ou movimentar o braço',
            icon: 'move-diagonal'
            }

            ],
       //categoria mal estar 
        malestar: [
            {
            id: 'Febre',
            label: 'Febre alta, calafrios ou sensação de corpo quente',
            icon: 'thermometer'
            },

            {
            id: 'Vômitos',
            label: 'Vômitos, enjoo ou diarreia',
            icon: 'shield-alert'
            },

            {
            id: 'Tontura',
            label: 'Tontura, fraqueza ou sensação de desmaio',
            icon: 'orbit'
            },

            {
            id: 'Fraqueza / Cansaço',
            label: 'Cansaço excessivo ou falta de energia',
            icon: 'battery-low'
            },

            {
            id: 'Dor no Corpo',
            label: 'Dores no corpo ou sensação de corpo pesado',
            icon: 'accessibility'
            },

            {
            id: 'Mal-estar Geral',
            label: 'Sensação de estar doente sem saber identificar',
            icon: 'circle-alert'
            },

            {
            id: 'Calafrios / Tremores',
            label: 'Calafrios ou tremores no corpo',
            icon: 'snowflake'
            },

            {
            id: 'Desidratação',
            label: 'Boca seca, pouca urina ou sensação de desidratação',
            icon: 'droplets'
            },

            {
            id: 'Perda de Apetite',
            label: 'Falta de fome ou dificuldade para se alimentar',
            icon: 'utensils-crossed'
            },

            {
            id: 'Suor Excessivo',
            label: 'Suor fora do normal ou suor frio',
            icon: 'cloud-drizzle'
            },

            {
            id: 'Palidez / Sensação Estranha',
            label: 'Palidez, sensação de fraqueza ou indisposição',
            icon: 'scan-face'
            },

            {
            id: 'Sintomas Virais',
            label: 'Corpo mole, indisposição ou sintomas gripais',
            icon: 'shield'
            }

        ],
       //categoria neuro
        neuro: [
            {
            id: 'AVC / Derrame',
            label: 'Formigamento ou perda de força súbita de um lado do corpo',
            icon: 'zap'
            },

            {
            id: 'Alteração da Fala',
            label: 'Dificuldade para falar ou entender o que falam',
            icon: 'message-circle'
            },

            {
            id: 'Dor de Cabeça Neurológica',
            label: 'Dor de cabeça intensa ou diferente do habitual',
            icon: 'brain'
            },

            {
            id: 'Tontura / Equilíbrio',
            label: 'Tontura forte ou dificuldade para se equilibrar',
            icon: 'compass'
            },

            {
            id: 'Desmaio / Consciência',
            label: 'Desmaio, apagão ou confusão mental',
            icon: 'moon'
            },

            {
            id: 'Convulsão',
            label: 'Convulsão ou movimentos involuntários',
            icon: 'activity'
            },

            {
            id: 'Sensibilidade Alterada',
            label: 'Dormência, formigamento ou alteração de sensibilidade',
            icon: 'hand'
            },

            {
            id: 'Coordenação Motora',
            label: 'Dificuldade para andar ou coordenar movimentos',
            icon: 'move'
            },

            {
            id: 'Sintomas Visuais Neurológicos',
            label: 'Perda de visão ou alteração visual repentina',
            icon: 'eye'
            },

            {
            id: 'Memória / Confusão',
            label: 'Esquecimento súbito ou dificuldade para pensar',
            icon: 'cpu'
            },

            {
            id: 'Dor Facial / Neuralgia',
            label: 'Dor intensa no rosto ou sensações elétricas',
            icon: 'triangle'
            },

            {
            id: 'Fraqueza Generalizada',
            label: 'Sensação de fraqueza ou perda de força',
            icon: 'battery-low'
            }

        ],
       
       //categorias gestantes
        gestante: [

        {
        id: 'Dores / Contrações',
        label: 'Contrações ou dores na gestação',
        icon: 'clock'
        },

        {
        id: 'Sangramento Gestacional',
        label: 'Sangramento vaginal',
        icon: 'droplet'
        },

        {
        id: 'Saída de Líquido',
        label: 'Saída de líquido ou suspeita de bolsa rompida',
        icon: 'waves'
        },

        {
        id: 'Movimentos do Bebê',
        label: 'Poucos movimentos ou ausência de movimentos do bebê',
        icon: 'heart'
        },

        {
        id: 'Dor Abdominal Gestacional',
        label: 'Dor abdominal ou desconforto na barriga',
        icon: 'circle'
        },

        {
        id: 'Sintomas Visuais',
        label: 'Visão embaçada ou pontos brilhantes',
        icon: 'eye'
        },

        {
        id: 'Dor de Cabeça Gestacional',
        label: 'Dor de cabeça persistente',
        icon: 'brain'
        },

        {
        id: 'Falta de Ar Gestacional',
        label: 'Falta de ar ou dificuldade para respirar',
        icon: 'wind'
        },

        {
        id: 'Inchaço',
        label: 'Inchaço repentino no rosto, mãos ou pernas',
        icon: 'expand'
        },

        {
        id: 'Febre Gestacional',
        label: 'Febre ou suspeita de infecção',
        icon: 'thermometer'
        },

        {
        id: 'Vômitos Gestacionais',
        label: 'Enjoo ou vômitos persistentes',
        icon: 'droplets'
        }

        ],

        infantil: [

        {
        id: 'Febre Infantil',
        label: 'Febre',
        icon: 'thermometer'
        },

        {
        id: 'Respiração Infantil',
        label: 'Respiração diferente ou chiado',
        icon: 'wind'
        },

        {
        id: 'Tosse Infantil',
        label: 'Tosse ou sintomas gripais',
        icon: 'cloud-rain'
        },

        {
        id: 'Vômitos Infantil',
        label: 'Vômitos ou diarreia',
        icon: 'droplets'
        },

        {
        id: 'Dor Infantil',
        label: 'Dor ou irritabilidade',
        icon: 'circle'
        },

        {
        id: 'Quedas Infantil',
        label: 'Queda ou machucado',
        icon: 'move-down'
        }

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
            },

            ],
            //perguntas traumas
            'Quedas': [
            {
            text: "Você caiu e bateu alguma parte do corpo que continua doendo agora?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Após a queda ficou difícil andar ou apoiar peso?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Você bateu a cabeça e percebeu tontura ou mal-estar depois?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A dor ou desconforto está piorando desde a queda?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Foi uma queda leve e você consegue se movimentar normalmente?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Fraturas': [
            {
            text: "Você percebe deformidade ou dificuldade importante para mexer o local?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "A dor aumenta muito ao tentar apoiar ou movimentar?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "O local ficou inchado ou roxo rapidamente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Você consegue mover o membro com algum desconforto?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas dor leve sem dificuldade para mexer?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Cortes': [
            {
            text: "O corte continua sangrando mesmo após pressionar o local?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "O corte parece profundo ou está aberto?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está difícil movimentar a região machucada?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "A dor está aumentando após o ferimento?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas um corte pequeno ou arranhão?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Contusão': [
            {
            text: "A região está muito dolorida ao toque?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "O local ficou inchado ou com hematoma grande?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está difícil usar normalmente essa parte do corpo?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor está piorando desde o impacto?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas desconforto leve ou pequeno roxo?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Torção Muscular': [
            {
            text: "Você sentiu dor logo após torcer ou fazer esforço?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está difícil apoiar peso ou caminhar?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Percebeu inchaço importante na região?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor piora quando tenta movimentar?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas desconforto leve e consegue usar normalmente?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Dor em Membro': [
            {
            text: "A dor começou após esforço ou batida recente?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está difícil usar o braço ou a perna normalmente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Percebe perda de força ou limitação do movimento?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor melhora quando fica em repouso?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "É apenas dor leve sem limitar movimentos?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Inchaço Articular': [
            {
            text: "A articulação ficou muito inchada após o ocorrido?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está difícil dobrar ou esticar normalmente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A região está quente ou muito sensível?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "O inchaço aumentou ao longo do dia?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas leve inchaço sem dificuldade para mexer?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Machucado Superficial': [
            {
            text: "A pele ficou sensível ou dolorida após o machucado?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "A área machucada aumentou de tamanho?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está difícil movimentar por causa do desconforto?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Percebe vermelhidão ou inchaço no local?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas um arranhão pequeno?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Dor Lombar Pós Esforço': [
            {
            text: "A dor começou após carregar peso ou fazer esforço?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está difícil ficar em pé ou andar normalmente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor piora quando muda de posição?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Descansar melhora parcialmente a dor?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "É apenas desconforto leve nas costas?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Trauma Leve na Cabeça': [
            {
            text: "Você bateu a cabeça e ficou com dor persistente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Teve tontura após a batida?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está com dificuldade para se concentrar depois do trauma?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor está melhorando com o tempo?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "Foi apenas uma batida leve sem outros sintomas?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Dor no Pescoço': [
            {
            text: "A dor começou após movimento brusco ou impacto?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está difícil virar o pescoço normalmente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor está irradiando para ombro ou braço?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "O desconforto está piorando?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas rigidez leve?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Dor no Ombro': [
            {
            text: "Está difícil levantar o braço normalmente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor começou após esforço ou impacto?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Sente dor ao movimentar o ombro?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Percebe perda de força no braço?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "É apenas desconforto leve sem limitar movimentos?",
            score: 3,
            stopOnPositive: false
            }
            ],
            //perguntas mal estar
            'Febre': [
            {
            text: "Você está com febre muito alta ou tremores intensos no corpo?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A febre está junto com dificuldade para respirar ou muito cansaço?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "A febre continua mesmo após repouso ou medicação habitual?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Você está conseguindo beber líquidos normalmente?",
            score: 0,
            stopOnPositive: false
            },
            {
            text: "É apenas sensação leve de febre ou corpo quente?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Vômitos': [
            {
            text: "Você está vomitando repetidamente sem conseguir beber líquidos?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Percebeu sangue ou coloração muito escura no vômito?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Teve vômitos ou diarreia várias vezes hoje?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está urinando menos que o habitual ou sentindo boca muito seca?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Foi apenas um episódio isolado e já melhorou?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Tontura': [
            {
            text: "Você sente que pode desmaiar ou cair?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A tontura veio junto com dor no peito ou falta de ar?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "A tontura impede você de andar normalmente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A tontura aparece quando levanta rápido?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É uma tontura leve e passageira?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Fraqueza / Cansaço': [
            {
            text: "Você está tão cansado que está difícil realizar tarefas simples?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A fraqueza apareceu de forma repentina?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está com sensação de corpo pesado ou sem energia?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Consegue caminhar normalmente?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "É apenas cansaço leve que melhora com descanso?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Dor no Corpo': [
            {
            text: "As dores estão muito fortes e dificultam se movimentar?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "As dores vieram junto com febre ou mal-estar importante?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Você sente dores em várias regiões do corpo?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "As dores pioram durante o dia?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "São dores leves que não impedem atividades?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Mal-estar Geral': [
            {
            text: "Você sente que está piorando rapidamente hoje?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está difícil realizar atividades normais?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Além do mal-estar, apareceu algum sintoma novo hoje?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Você está conseguindo se alimentar normalmente?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas indisposição leve?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Calafrios / Tremores': [
            {
            text: "Os tremores estão intensos e difíceis de controlar?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Os calafrios vieram junto com febre alta?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está sentindo muito frio mesmo em ambiente aquecido?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Os tremores dificultam segurar objetos?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "São calafrios leves e passageiros?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Desidratação': [
            {
            text: "Você passou muitas horas sem conseguir beber líquidos?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está urinando muito menos que o habitual?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Sua boca está muito seca ou sente sede intensa?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Sente tontura ao levantar?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Consegue beber líquidos normalmente?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Perda de Apetite': [
            {
            text: "Você está há muitas horas sem conseguir se alimentar?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Perdeu totalmente a vontade de comer hoje?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Sente enjoo quando tenta comer?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está conseguindo beber líquidos normalmente?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "É apenas redução leve do apetite?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Suor Excessivo': [
            {
            text: "Está com suor frio acompanhado de mal-estar importante?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "O suor veio junto com dor no peito ou dificuldade para respirar?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Está transpirando mais que o habitual sem esforço?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "O suor está causando sensação de fraqueza?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Foi apenas um episódio leve?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Palidez / Sensação Estranha': [
            {
            text: "Você ficou muito pálido junto com tontura ou fraqueza?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Percebe sensação de que pode desmaiar?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está sentindo algo diferente do normal sem conseguir explicar?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Isso está dificultando suas atividades?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas sensação leve de indisposição?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Sintomas Virais': [
            {
            text: "Você está com febre e dores no corpo?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está com tosse, nariz escorrendo ou sintomas gripais?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Está sentindo muito cansaço ou indisposição?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Os sintomas estão piorando rapidamente?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "São sintomas leves e você consegue manter rotina normal?",
            score: 3,
            stopOnPositive: false
            }
            ],
            //perguntas neuro
        'AVC / Derrame': [
            {
            text: "Você percebeu perda de força ou dormência que começou de repente em um lado do corpo?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Está com dificuldade para sorrir ou percebeu um lado do rosto diferente?",
            score: 10,
            stopOnPositive: false
            },
            {
            text: "Está com dificuldade para falar ou entender o que falam?",
            score: 10,
            stopOnPositive: false
            },
            {
            text: "Os sintomas começaram nas últimas horas?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Os sintomas já melhoraram parcialmente?",
            score: 5,
            stopOnPositive: false
            }
            ],

        'Alteração da Fala': [
            {
            text: "Você está com dificuldade para falar frases simples?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Está entendendo normalmente o que as pessoas dizem?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "As palavras estão saindo trocadas ou enroladas?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Isso começou de forma repentina?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "É apenas rouquidão ou mudança leve na voz?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Dor de Cabeça Neurológica': [
            {
            text: "A dor começou muito forte ou diferente do habitual?",
            score: 9,
            stopOnPositive: true
            },
            {
            text: "A dor veio junto com náusea, vômito ou sensibilidade à luz?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Você já teve dores parecidas anteriormente?",
            score: 4,
            stopOnPositive: false
            },
            {
            text: "A dor está dificultando suas atividades normais?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "A dor é leve e suportável?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Tontura / Equilíbrio': [
            {
            text: "A tontura está impedindo você de ficar em pé ou andar?",
            score: 9,
            stopOnPositive: true
            },
            {
            text: "Você sente o ambiente girando ao seu redor?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A tontura começou de repente?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Você teve enjoo junto com a tontura?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "A tontura é leve e passa rapidamente?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Desmaio / Consciência': [
            {
            text: "Você desmaiou ou perdeu a consciência?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Ficou confuso ou demorou para voltar ao normal?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Sentiu tontura antes de acontecer?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Foi um episódio curto e você já está bem?",
            score: 3,
            stopOnPositive: false
            },
            {
            text: "Isso já aconteceu antes?",
            score: 4,
            stopOnPositive: false
            }
            ],

        'Convulsão': [
            {
            text: "Você teve movimentos involuntários ou tremores fortes?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Perdeu consciência durante o episódio?",
            score: 10,
            stopOnPositive: false
            },
            {
            text: "Após o episódio ficou sonolento ou confuso?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Foi a primeira vez que aconteceu?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "O episódio já terminou completamente?",
            score: 4,
            stopOnPositive: false
            }
            ],

        'Sensibilidade Alterada': [
            {
            text: "Você perdeu sensibilidade em alguma parte do corpo?",
            score: 8,
            stopOnPositive: true
            },
            {
            text: "Está sentindo formigamento contínuo?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "A alteração começou de repente?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está afetando apenas um lado do corpo?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "É apenas sensação leve e passageira?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Coordenação Motora': [
            {
            text: "Está com dificuldade para andar ou manter equilíbrio?",
            score: 9,
            stopOnPositive: true
            },
            {
            text: "Percebe dificuldade para segurar objetos?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Os movimentos parecem descoordenados?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Isso apareceu de forma repentina?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Consegue realizar atividades normalmente?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Sintomas Visuais Neurológicos': [
            {
            text: "Você perdeu parte da visão ou escureceu de repente?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Está vendo pontos brilhantes ou imagens distorcidas?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A alteração apareceu junto com dor de cabeça?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Está difícil enxergar normalmente?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Foi algo leve que já melhorou?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Memória / Confusão': [
            {
            text: "Você ficou desorientado ou sem reconhecer onde estava?",
            score: 9,
            stopOnPositive: true
            },
            {
            text: "Está esquecendo acontecimentos recentes?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está difícil manter uma conversa normal?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Isso começou recentemente?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Está apenas com dificuldade leve de concentração?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Dor Facial / Neuralgia': [
            {
            text: "A dor apareceu de repente como choque ou pontada?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A dor piora ao tocar o rosto?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Está difícil mastigar ou falar?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "A dor acontece em crises curtas?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É um desconforto leve e suportável?",
            score: 2,
            stopOnPositive: false
            }
            ],

        'Fraqueza Generalizada': [
            {
            text: "Você está com dificuldade para levantar ou caminhar?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A fraqueza começou de repente?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está sentindo cansaço fora do habitual?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "A fraqueza está piorando?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Consegue fazer suas atividades normalmente?",
            score: 2,
            stopOnPositive: false
            }
            ],

            //perguntas gestantes
            'Dores / Contrações': [
            {
            text: "Você está sentindo contrações ou dores que estão ficando mais frequentes?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "As dores estão acontecendo em intervalos regulares?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "As contrações continuam mesmo quando você descansa?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "As dores estão dificultando andar ou descansar?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "Você sente apenas endurecimentos leves que passam sozinhos?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Sangramento Gestacional': [
            {
            text: "Você percebeu sangramento vaginal moderado ou intenso?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "O sangramento veio junto com dor abdominal?",
            score: 9,
            stopOnPositive: false
            },
            {
            text: "O sangramento aumentou nas últimas horas?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Você já teve mais de um episódio de sangramento hoje?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Foi apenas pequeno escape sem outros sintomas?",
            score: 3,
            stopOnPositive: false
            }
            ],

            'Saída de Líquido': [
            {
            text: "Percebeu saída contínua de líquido pela vagina?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "O líquido continua saindo mesmo após troca de roupa?",
            score: 9,
            stopOnPositive: false
            },
            {
            text: "A saída de líquido veio acompanhada de contrações?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Percebeu aumento importante da quantidade de líquido?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Foi apenas aumento discreto de secreção habitual?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Movimentos do Bebê': [
            {
            text: "Você percebeu diminuição importante dos movimentos do bebê?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Ficou várias horas sem perceber movimentos?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "Os movimentos parecem mais fracos que o habitual?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Precisou estimular para sentir movimentos?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Os movimentos parecem normais hoje?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Dor Abdominal Gestacional': [
            {
            text: "Está com dor abdominal forte e contínua?",
            score: 9,
            stopOnPositive: true
            },
            {
            text: "A dor piora ao caminhar ou mudar de posição?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "A dor veio acompanhada de náusea ou vômitos?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "A dor impede suas atividades normais?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas desconforto leve ou sensação de peso?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Sintomas Visuais': [
            {
            text: "Sua visão ficou muito embaçada ou escureceu de repente?",
            score: 10,
            stopOnPositive: true
            },
        
            {
            text: "Você está vendo pontos brilhantes ou flashes?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A alteração visual apareceu junto com dor de cabeça?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A alteração visual está dificultando enxergar normalmente?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Foi apenas sensação leve de vista cansada?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Dor de Cabeça Gestacional': [
            {
            text: "A dor está muito forte ou diferente do habitual?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A dor apareceu junto com alteração visual?",
            score: 9,
            stopOnPositive: false
            },
            {
            text: "A dor não melhorou após repouso?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está atrapalhando suas atividades normais?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É uma dor leve que costuma melhorar sozinha?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Falta de Ar Gestacional': [
            {
            text: "Você sente falta de ar mesmo parada ou falando?",
            score: 10,
            stopOnPositive: true
            },
            {
            text: "A dificuldade para respirar começou recentemente e piorou?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está difícil caminhar pequenas distâncias?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "Está junto com tosse ou sintomas gripais?",
            score: 4,
            stopOnPositive: false
            },
            {
            text: "A sensação aparece apenas em esforço maior?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Inchaço': [
            {
            text: "O inchaço apareceu rapidamente no rosto ou mãos?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "O inchaço veio junto com dor de cabeça?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Percebe aumento importante do inchaço hoje?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está dificultando movimentar mãos ou pés?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É um inchaço leve que melhora com repouso?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Febre Gestacional': [
            {
            text: "Está com febre e mal-estar importante?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "A febre continua mesmo após repouso?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está com dor para urinar ou desconforto urinário?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Está sentindo tremores ou calafrios?",
            score: 6,
            stopOnPositive: false
            },
            {
            text: "É apenas sensação leve de calor?",
            score: 2,
            stopOnPositive: false
            }
            ],

            'Vômitos Gestacionais': [
            {
            text: "Você está com dificuldade para beber líquidos?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está vomitando várias vezes ao longo do dia?",
            score: 7,
            stopOnPositive: false
            },
            {
            text: "Percebe boca seca ou redução da urina?",
            score: 8,
            stopOnPositive: false
            },
            {
            text: "Está conseguindo se alimentar parcialmente?",
            score: 5,
            stopOnPositive: false
            },
            {
            text: "É apenas enjoo leve e ocasional?",
            score: 2,
            stopOnPositive: false
            }
            ]
    }   
};

window.quizData = quizData;