<div class="modal fade"
     id="popupForm"
     tabindex="-1"
     aria-labelledby="popupFormLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content shadow border-0">

            {{-- HEADER --}}
            <div class="modal-header bg-light">

                <h5 class="modal-title text-danger fw-bold"
                    id="popupFormLabel">

                    <i class="bi bi-car-front-fill"></i>

                    <span id="modalTitle">
                        NOUVELLE VOITURE
                    </span>

                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fermer">
                </button>

            </div>

            {{-- BODY --}}
            <div class="modal-body">

                {{-- Zone d'affichage des erreurs --}}
                <div id="formErrors"
                     class="alert alert-danger d-none">
                </div>

                <form id="voitureForm">

                    @csrf

                    <input type="hidden"
                           name="id"
                           id="rowId">

                    {{-- =========================
                    IDENTIFICATION
                    ========================== --}}
                    <div class="mb-3 border-bottom pb-1 text-danger fw-bold text-uppercase small">

                        <i class="bi bi-card-text"></i>
                        Identification du véhicule

                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-md-4">

                            <div class="form-floating">

                                <input type="text"
                                       class="form-control text-uppercase"
                                       id="immatriculation"
                                       name="immatriculation"
                                       placeholder="AA-123-AA"
                                       maxlength="255"
                                       required>

                                <label for="immatriculation">
                                    Immatriculation
                                </label>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-floating">

                                <input type="text"
                                       class="form-control"
                                       id="marque"
                                       name="marque"
                                       placeholder="Marque"
                                       maxlength="255"
                                       required>

                                <label for="marque">
                                    Marque
                                </label>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-floating">

                                <input type="text"
                                       class="form-control"
                                       id="modele"
                                       name="modele"
                                       placeholder="Modèle"
                                       maxlength="255"
                                       required>

                                <label for="modele">
                                    Modèle
                                </label>

                            </div>

                        </div>

                    </div>

                    {{-- =========================
                    ÉTAT DU VÉHICULE
                    ========================== --}}
                    <div class="mb-3 border-bottom pb-1 text-danger fw-bold text-uppercase small">

                        <i class="bi bi-speedometer2"></i>
                        État du véhicule

                    </div>

                    <div class="row g-3 mb-3">

                        <div class="col-md-6">

                            <div class="input-group">

                                <div class="form-floating flex-grow-1">

                                    <input type="number"
                                           class="form-control"
                                           id="kilometrage"
                                           name="kilometrage"
                                           placeholder="Kilométrage"
                                           min="0"
                                           step="1"
                                           value="0">

                                    <label for="kilometrage">
                                        Kilométrage
                                    </label>

                                </div>

                                <span class="input-group-text">
                                    km
                                </span>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-floating">

                                <select class="form-select"
                                        id="statut"
                                        name="statut"
                                        required>

                                    <option value="disponible">
                                        Disponible
                                    </option>

                                    <option value="réservé">
                                        Réservé
                                    </option>

                                    <option value="indisponible">
                                        Indisponible
                                    </option>

                                </select>

                                <label for="statut">
                                    Statut du véhicule
                                </label>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">

                    <i class="bi bi-x-circle"></i>
                    Annuler

                </button>

                <button type="button"
                        class="btn btn-primary rounded-pill px-4"
                        id="saveButton"
                        onclick="saveData()">

                    <i class="bi bi-save"></i>
                    Enregistrer

                </button>

            </div>

        </div>

    </div>

</div>