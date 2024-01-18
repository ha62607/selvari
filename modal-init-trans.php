<!-- Modal -->
<div class="modal" tabindex="-1" id="init-trans">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Noudamme tilitapahtumat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 style="text-align: center;">Noudamme linkitetyn tilisi tilitapahtumat</h5>

                <div style="width:90%; text-align: center; margin:15px;">



                    <div class="spinner-border text-success" role="status" id="spinner">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="alert alert-success" role="alert" id="dbdone" >
                        Tapahtumat ovat nyt tietokannassa.
                    </div>

                </div>

                <p style="text-align: center; font-style: italic;">Odota hetki sulkematta ikkunaa.
                </p>


                <p  style="text-align: center; font-style: italic;" id="trans-status">
                    Avataan yhteys pankkiin...

                </p>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulje</button>
            </div>
        </div>
    </div>
</div>

