@extends('layouts.master')

@section('title', 'AE - Contatti')

@section('active_contatti', 'active')

@section('body')
    <div class="container py-5">
        <div class="row mb-5 text-center fade-in">
            <div class="col-12">
                <h1 class="display-4 fw-bold text-dark mb-3">Contatti</h1>
                <p class="lead text-body-secondary col-md-8 mx-auto">Siamo qui per rispondere alle tue domande e supportarti
                    nei
                    progetti di mobilità europea.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-envelope-paper-fill text-primary fs-3"></i>
                        <h2 class="h5 fw-bold mb-0">Invia un Messaggio</h2>
                    </div>

                    <p class="small text-body-secondary mb-4">I campi contrassegnati con l'asterisco <span
                            class="text-danger fw-bold">*</span> sono obbligatori.
                    </p>

                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome_cognome" class="form-label">
                                    <i class="bi bi-person me-1"></i>Nome e Cognome <span class="text-danger"
                                        aria-hidden="true">*</span>
                                </label>
                                <input type="text" class="form-control" id="nome_cognome" name="nome_cognome" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>Email <span class="text-danger"
                                        aria-hidden="true">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="oggetto" class="form-label">
                                <i class="bi bi-tag me-1"></i>Oggetto <span class="text-danger" aria-hidden="true">*</span>
                            </label>
                            <select class="form-select" id="oggetto" name="oggetto" required>
                                <option value="">Seleziona un oggetto...</option>
                                <option value="informazioni-generali">Informazioni Generali</option>
                                <option value="corpo-europeo">Corpo Europeo di Solidarietà</option>
                                <option value="scambi-giovanili">Scambi Giovanili</option>
                                <option value="corsi-formazione">Corsi di Formazione</option>
                                <option value="progetti-disponibili">Progetti Disponibili</option>
                                <option value="partnership">Partnership e Collaborazioni</option>
                                <option value="altro">Altro</option>
                            </select>
                        </div>

                        <div class="mt-3">
                            <label for="messaggio" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Messaggio <span class="text-danger"
                                    aria-hidden="true">*</span>
                            </label>
                            <textarea class="form-control" id="messaggio" name="messaggio" rows="5"
                                placeholder="Scrivi qui il tuo messaggio..." required></textarea>
                        </div>

                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                <label class="form-check-label" for="privacy">
                                    Accetto il trattamento dei dati personali secondo la
                                    <a href="#" class="text-decoration-none">Privacy Policy</a> <span class="text-danger"
                                        aria-hidden="true">*</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn px-4 py-2 btn-ae btn-ae-pill btn-ae-primary w-100">
                                <i class="bi bi-send me-2"></i>Invia Messaggio
                            </button>
                        </div>
                    </form>

                    <div class="pt-3 mt-4 border-top">
                        <small class="text-body-secondary">
                            <i class="bi bi-info-circle me-1"></i>
                            Ti risponderemo entro 24-48 ore lavorative. Per richieste urgenti, contattaci direttamente via
                            telefono.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-info-circle-fill text-primary fs-3"></i>
                        <h2 class="h5 fw-bold mb-0">Informazioni di Contatto</h2>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-start gap-3 py-2 border-bottom">
                            <i class="bi bi-geo-alt-fill text-primary fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block">Indirizzo</strong>
                                <span class="text-body-secondary">C/o CSV, Via Salgari 43/b<br>25125 Brescia</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 py-2 border-bottom">
                            <i class="bi bi-telephone-fill text-primary fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block">Telefono</strong>
                                <a href="tel:+390302284900" class="text-decoration-none">+39 030 22 84 900</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 py-2 border-bottom">
                            <i class="bi bi-envelope-fill text-primary fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block">Email</strong>
                                <a href="mailto:info@ateliereuropeo.eu"
                                    class="text-decoration-none">info@ateliereuropeo.eu</a>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 py-2">
                            <i class="bi bi-envelope-at-fill text-primary fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block">PEC</strong>
                                <a href="mailto:ateliereuropeo@pec.it"
                                    class="text-decoration-none">ateliereuropeo@pec.it</a>
                            </div>
                        </li>
                    </ul>

                    <div class="pt-3 border-top">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            <h3 class="h6 fw-bold mb-0">Dati Fiscali</h3>
                        </div>
                        <p class="small text-body-secondary mb-1"><strong>P.IVA:</strong> 03747110983</p>
                        <p class="small text-body-secondary mb-0"><strong>C.F:</strong> 98174020176</p>
                    </div>

                    <div class="pt-3 mt-3 border-top">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-share text-primary fs-4"></i>
                            <h3 class="h6 fw-bold mb-0">Seguici sui Social</h3>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://www.facebook.com/AtelierEuropeo/" target="_blank"
                                class="btn btn-ae btn-ae-square btn-ae-outline-primary px-3 py-2 d-inline-flex align-items-center gap-2">
                                <i class="bi bi-facebook"></i><span>Facebook</span>
                            </a>
                            <a href="https://www.instagram.com/ateliereuropeo/" target="_blank"
                                class="btn btn-ae btn-ae-square btn-ae-outline-primary px-3 py-2 d-inline-flex align-items-center gap-2">
                                <i class="bi bi-instagram"></i><span>Instagram</span>
                            </a>
                            <a href="https://www.linkedin.com/company/atelier-europeo/" target="_blank"
                                class="btn btn-ae btn-ae-square btn-ae-outline-primary px-3 py-2 d-inline-flex align-items-center gap-2">
                                <i class="bi bi-linkedin"></i><span>LinkedIn</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="p-4 pb-3">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-geo-alt-fill text-primary fs-3"></i>
                            <h2 class="h5 fw-bold mb-0">Dove Siamo</h2>
                        </div>
                    </div>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2796.305594954824!2d10.187656076858766!3d45.50379962979921!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478177b738542d99%3A0x4884934139747595!2sVia%20Emilio%20Salgari%2C%2043b%2C%2025125%20Brescia%20BS!5e0!3m2!1sit!2sit!4v1689786543210!5m2!1sit!2sit"
                        width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <div class="p-4 border-top">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-8">
                                <small class="text-body-secondary">
                                    <i class="bi bi-clock me-1"></i>
                                    <strong>Orari di ricevimento:</strong> Lunedì - Venerdì: 9:00 - 17:00
                                </small>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="https://www.google.com/maps/dir//Via+Emilio+Salgari,+43b,+25125+Brescia+BS"
                                    target="_blank"
                                    class="btn btn-ae btn-ae-square btn-ae-outline-primary px-3 py-2 d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-signpost"></i><span>Ottieni Indicazioni</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4">
            <div class="col-12 text-center mb-4">
                <h2 class="h3 fw-bold text-dark mb-2">Domande Frequenti</h2>
                <p class="text-body-secondary">Trova risposte rapide ai dubbi più comuni sui nostri programmi.</p>
            </div>

            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
                    <div class="accordion accordion-flush" id="faqAccordion">

                        <div class="accordion-item border-0 border-bottom mb-2 pb-2">
                            <h2 class="accordion-header" id="faqHeadingOne">
                                <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne"
                                    aria-expanded="false" aria-controls="faqCollapseOne">
                                    Come posso candidarmi a un progetto?
                                </button>
                            </h2>
                            <div id="faqCollapseOne" class="accordion-collapse collapse" aria-labelledby="faqHeadingOne"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-body-secondary pt-0">
                                    Puoi candidarti direttamente dalla pagina del progetto di tuo interesse, cliccando sul
                                    pulsante "Candidati ora" e compilando il modulo dedicato. Ti consigliamo di preparare in
                                    anticipo il tuo Curriculum Vitae e una breve lettera motivazionale.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom mb-2 pb-2">
                            <h2 class="accordion-header" id="faqHeadingTwo">
                                <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo"
                                    aria-expanded="false" aria-controls="faqCollapseTwo">
                                    Quali sono i requisiti di età per partecipare?
                                </button>
                            </h2>
                            <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-body-secondary pt-0">
                                    La maggior parte dei nostri programmi, come il Corpo Europeo di Solidarietà e gli Scambi
                                    Giovanili, sono rivolti a giovani tra i 18 e i 30 anni. Tuttavia, alcune opportunità
                                    come i Corsi di Formazione (Training Courses) non hanno limiti di età massima. Verifica
                                    sempre i dettagli specifici di ogni progetto.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="faqHeadingThree">
                                <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree"
                                    aria-expanded="false" aria-controls="faqCollapseThree">
                                    Ci sono dei costi di partecipazione?
                                </button>
                            </h2>
                            <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-body-secondary pt-0">
                                    I progetti finanziati dal programma Erasmus+ e dal Corpo Europeo di Solidarietà coprono
                                    solitamente i costi di vitto, alloggio e rimborsano parzialmente o totalmente le spese
                                    di viaggio. Potrebbe essere richiesta una piccola quota associativa per l'iscrizione ad
                                    Atelier Europeo.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection