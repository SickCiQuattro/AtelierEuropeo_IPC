@php
    $categoriesInfo = [
        'CF' => [
            'title' => 'Corsi di Formazione',
            'icon' => 'bi-mortarboard-fill',
            'colorClass' => 'text-prog-cf',
            'subtitle' => 'Opportunità di sviluppo professionale intensivo per chi lavora o fa volontariato nel mondo dei giovani.',
            'sections' => [
                "Cos'è e quanto dura:" => "Corsi intensivi di 3-10 giorni basati sull'educazione non formale, per sviluppare competenze in project management, inclusione e leadership.",
                'Chi può partecipare:' => 'Youth Workers, operatori del Terzo Settore, educatori, formatori e volontari esperti.',
            ],
            'listTitle' => 'Costi e Coperture:',
            'listIntro' => 'Investimento minimo per un grande beneficio professionale. Erasmus+ copre totalmente:',
            'listItems' => [
                'Vitto, alloggio, assicurazione e materiali didattici',
                'Rimborso Viaggio a scaglioni (es. da 100 a 499 km fino a 180€; oltre 3000 km fino a 530€)',
            ],
        ],
        'SG' => [
            'title' => 'Scambi Giovanili',
            'icon' => 'bi-people-fill',
            'colorClass' => 'text-prog-sg',
            'subtitle' => 'Esperienze di mobilità internazionale per abbattere le barriere culturali e migliorare le lingue.',
            'sections' => [
                "Cos'è e quanto dura:" => 'Incontri interculturali intensivi della durata di 7-10 giorni.',
                'Chi può partecipare:' => 'Giovani dai 13 ai 30 anni (nessun limite di età per i Team Leader). Basta un livello base di inglese e la voglia di mettersi in gioco. Aperti a tutti, l\'inclusività è al primo posto!',
            ],
            'listTitle' => 'Coperture (Esperienza Gratuita):',
            'listIntro' => "Il programma Erasmus+ copre interamente l'esperienza:",
            'listItems' => [
                'Viaggio (rimborsato con massimale)',
                'Vitto, alloggio e attivita del programma',
                'Assicurazione sanitaria',
                'Rilascio del certificato ufficiale <em>YouthPass</em>',
            ],
        ],
        'CES' => [
            'title' => 'Corpo Europeo di Solidarietà',
            'icon' => 'bi-heart-fill',
            'colorClass' => 'text-prog-ces',
            'subtitle' => "Un'opportunità per contribuire alla solidarietà europea e crescere personalmente e professionalmente.",
            'sections' => [
                "Cos'è e quanto dura:" => 'Progetti di volontariato in Europa della durata variabile dalle 2 settimane a 1 anno.',
                'Chi può partecipare:' => 'Giovani dai 18 ai 30 anni. Non sono richieste competenze specifiche pregresse, ma solo una forte motivazione.',
            ],
            'listTitle' => 'Cosa include (Totalmente finanziato):',
            'listIntro' => '',
            'listItems' => [
                'Spese di viaggio (con massimale in base alla distanza)',
                'Vitto e alloggio garantiti',
                'Assicurazione sanitaria completa',
                '<em>Pocket money</em> mensile per le spese personali',
                'Supporto linguistico online (OLS)',
            ],
        ],
    ];
@endphp

@foreach ($categoriesInfo as $tag => $info)
    <div class="modal fade" id="infoModal-{{ $tag }}" tabindex="-1" aria-labelledby="infoModalLabel-{{ $tag }}"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-4" id="infoModalLabel-{{ $tag }}">
                        <i class="bi {{ $info['icon'] }} {{ $info['colorClass'] }}"></i>
                        <span class="{{ $info['colorClass'] }}">{{ $info['title'] }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary fs-6 mb-3">{{ $info['subtitle'] }}</p>
                    <hr>

                    @foreach ($info['sections'] as $sectionTitle => $sectionText)
                        <p class="mb-2"><b>{{ $sectionTitle }}</b> {{ $sectionText }}</p>
                    @endforeach

                    <p class="mt-3 mb-2"><b>{{ $info['listTitle'] }}</b></p>
                    @if (!empty($info['listIntro']))
                        <p class="mb-2">{{ $info['listIntro'] }}</p>
                    @endif

                    <ul class="mb-0">
                        @foreach ($info['listItems'] as $item)
                            <li>{!! $item !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach