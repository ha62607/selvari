<script>
    $(document).ready(function (e) {



        $("#alias").on('submit',(function(e) {
            e.preventDefault();

            var validform = $('form[id="alias"]').valid();

            if(validform )
            {
                $.ajax({
                    url: "setaliastext.php",
                    type: "POST",
                    data:  new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend : function()
                    {
                    },
                    success: function(data)
                    {

                            // view uploaded file.
                            $("#alias").trigger("reset");
                            $('#close-alias-modal').click();

                    },
                    error: function(e)
                    {
                    }
                });
            }
        }));

        $('form[id="selite"]').validate({
            rules: {
                selite: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                }
            },
            messages: {
                selite:
                    {
                        required: "Selite puuttuu",
                        minlength: "Selitteen min pituus 10 merkkiä",
                        maxlength: "Selitteen max pituus 500 merkkiä"

                    }
            }
        });

    });



</script>


<!-- Modal -->
<div class="modal" tabindex="-1" id="tapahtuma">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pankkitapahtuma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="border:1px solid blue;">




                <div id="err"></div>


                <form id="alias" action="#" method="post" enctype="multipart/form-data">


                    <div class="form-group">
                        <label for="alias">Saaja / maksaja:</label>
                        <div class="tapahtumaform" id="debtor"></div>
                    </div>

                    <div class="form-group">
                        <label for="alias">Tilinumero / IBAN:</label>
                        <div class="tapahtumaform" id="iban"></div>
                    </div>

                    <input type="hidden" name="tid" id="tid" value=""/>
                    <input type="hidden" name="transid" id="transid" value=""/>
                    <input type="hidden" name="bban" id="bban" value=""/>

                    <div class="form-group">
                        <label for="alias">Aseta nimeys tapahtumalle:</label>
                        <input type="text" id="alias" name="alias"  aria-describedby="seliteHelpBlock" class="form-control"/><br/>

                    </div>
                    <div class="form-group">
                        <button name="submit" type="submit" id="selitebutton" class="btn btn-secondary">Aseta alias</button>
                    </div>
                    <div class="xform-group">
                        <i>Tämän maksajan tai saajan kaikki tapahtumat nimetään uudella asettammalla nimelläsi.</i>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-alias-modal">Sulje</button>
            </div>
        </div>
    </div>
</div>

