<script>
    $(document).ready(function (e) {

        $("#removekuvaform").on('submit',(function(e) {
        e.preventDefault();

        var formData = {
            tid: $("#removetapahtuma").val(),
            kid: $("#removekuitti").val()
        };

        $.ajax({
            url: "removekuva.php",
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
                $('#removekuittimodal').click();

            },
            error: function(e)
            {
            }
        });

    }));
    });

</script>

        <!-- Modal -->
<div class="modal" tabindex="-1" id="showfile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kuitti tilitapahtumasta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="width:100%; text-align: center;">

                <img src="./img/pdf.png"  id="kuittishow"  style="display:none;" />

                <img src="./img/pdf.png"  id="pdfshow"  style="display:none;" />

                <br/><br/>
                <a href="" target="_Blank" id="pdflink" style="display:none;">Näytä PDF &rsaquo;</a>

                <!--
                <iframe style="display:none;" src = "<?php echo($SITE) ?>ViewerJS/#<?php echo($SITE) ?>getfile.php?kuitti=9" width='400' height='300' allowfullscreen webkitallowfullscreen id="kuittipdfframe"></iframe>
                -->

                <br/><br/>
             <form id="removekuvaform" method="POST" action="removekuva.php">
                 <input type="hidden" name="removekuitti" id="removekuitti"/>
                 <input type="hidden" name="removetapahtuma" id="removetapahtuma"/>
                 <div class="form-group">
                     <button name="submit" type="submit" id="removeimg" class="btn btn-secondary">Poista kuva</button>
                 </div>

             </form>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="removekuittimodal">Sulje</button>
            </div>
        </div>
    </div>
</div>

