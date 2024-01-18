<script>
    $(document).ready(function (e) {



        $("#tilibutton").on('click',(function(e) {
            e.preventDefault();

            var validform = $('form[id="tiliform"]').valid();

            if(validform )
            {
                var form = new FormData(document.getElementById("tiliform"));

                $.ajax({
                    url: "settili.php",
                    type: "POST",
                    data:  form,
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend : function()
                    {
                    },
                    success: function(data)
                    {

                            // view uploaded file.
                            $("#tiliform").trigger("reset");
                            $('#close-tili-modal').click();

                    },
                    error: function(e)
                    {
                    }
                });
            }
        }));

        $('form[id="tiliform"]').validate({
            rules: {
                tilikoodi: {
                    required: true,
                    minlength: 4,
                    maxlength: 4
                }
            },
            messages: {
                tilikoodi:
                    {
                        required: "Tilkoodi puuttuu",
                        minlength: "Tilikoodi 4 numeroa",
                        maxlength: "Tilikoodi 4 numeroa"

                    }
            }
        });

    });



</script>
<link href="./autocom/content/styles.css" rel="stylesheet" />

<script type="text/javascript" src="./autocom/scripts/jquery.mockjax.js"></script>
<script type="text/javascript" src="./autocom/src/jquery.autocomplete.js"></script>
<script type="text/javascript" src="./autocom/scripts/countries.js"></script>
<script type="text/javascript" src="./tilit.php"></script>

<script type="text/javascript" src="./autocom/scripts/demo.js"></script>
<!-- Modal -->
<div class="modal" tabindex="-1" id="tili">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pankkitapahtuma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="border:1px solid blue;">



                <div id="loadmock"></div>
               <div id="err"></div>


                <form id="tiliform" action="#" method="post" enctype="multipart/form-data">


                    <div class="form-group">
                        <label for="alias">Saaja / maksaja:</label>
                        <div class="tapahtumaform" id="debtor"></div>
                    </div>

                    <div class="form-group">
                        <label for="alias">Summa:</label>
                        <span class="tapahtumaform" id="tamount"></span>
                    </div>

                    <input type="hidden" name="tid" id="tid" value=""/>
                    <input type="hidden" name="sid" id="sid" value="0"/>
                    <input type="hidden" name="transid" id="transid" value=""/>
                    <input type="hidden" name="amount" id="amount" value=""/>

                    <div class="form-group" style="max-width:550px; width:100%;">
                        <h6>Aseta tili:</h6>
                    <div style="position: relative; height: 80px; border:0px solid red; max-width:550px; width:80%;">
                        <input type="text" name="country" id="autocomplete-ajax" style="position: absolute; z-index: 2; background: white;"/>
                        <input type="text" name="country" id="autocomplete-ajax-x" disabled="disabled" style="color: #CCC; position: absolute; background: white; z-index: 1;"/>
                    </div>
                    <div id="selction-ajax"></div>
                    <input type="hidden" id="tilikoodi" name="tilikoodi" value=""/>

                        <br/>
                        <h6>ALV-kanta</h6>
                        <?php
                        $alvit = alv();

                        $ind = 0;
                        foreach ($alvit as $alv) {
                           ?>
                        <span class="alvikanta"><input type="radio" value="<?php echo($alv) ?>" name="alv" class="alvradio"> <?php echo($alv) ?> %</span>
                            <?php
                        }


                        ?>


                    </div>

                    <div style="clear:both; width:100%; height:15px;"></div>


                    <div class="form-group">
                        <button name="tilibutton" type="button" id="tilibutton" class="btn btn-secondary">Tallenna</button>
                    </div>
                    <div class="xform-group">
                        <i>Lorem ipsum</i>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-tili-modal">Sulje</button>
            </div>
        </div>
    </div>
</div>

