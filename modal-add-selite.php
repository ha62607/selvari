<script>
    $(document).ready(function (e) {



        $("#selite").on('submit',(function(e) {
            e.preventDefault();

            var validform = $('form[id="selite"]').valid();

            if(validform )
            {
                $.ajax({
                    url: "ajaxselite.php",
                    type: "POST",
                    data:  new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend : function()
                    {
                        //$("#preview").fadeOut();
                        $("#err").fadeOut();
                    },
                    success: function(data)
                    {
                        if(data=='invalid')
                        {
                            // invalid file format.
                            $("#err").html("Invalid File !").fadeIn();
                        }
                        else
                        {
                            // view uploaded file.
                            $("#selite").trigger("reset");
                            $('#close-selite-modal').click();

                            //$("#kuitti")[0].reset();
                        }

                        console.log("IMG UPLOAD:"+data);
                    },
                    error: function(e)
                    {
                        $("#err").html(e).fadeIn();
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
                        minlength: "Selitten min pituus 10 merkkiä",
                        maxlength: "Selitten max pituus 500 merkkiä"

                    }
            }
        });

    });



</script>


<!-- Modal -->
<div class="modal" tabindex="-1" id="addselite">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lisää selite tilitapahtumasta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">


                <p><span id="lataaid"></span> &euro;</p>

                <div id="err"></div>


                <form id="selite" action="#" method="post" enctype="multipart/form-data">



                    <input type="hidden" name="tid" id="tid" value=""/>
                    <input type="hidden" name="transid" id="transid" value=""/>

                    <div class="form-group">
                        <label for="selite">Selite </label>
                        <textarea id="selite" name="selite" cols="100" rows="5" aria-describedby="seliteHelpBlock" class="form-control"></textarea>

                    </div>
                    <div class="form-group">
                        <button name="submit" type="submit" id="selitebutton" class="btn btn-secondary">Lisää selite</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-selite-modal">Sulje</button>
            </div>
        </div>
    </div>
</div>

