<script>
    $(document).ready(function (e) {

        $(document).on('change', '.currency', function() {
            $(this).val($(this).val().replace(/,/g, '.'));
        });

        $.validator.addMethod("currency", function (value, element) {

            var floatValues =  /[+-]?([0-9]*[.])?[0-9]+/;
            if (value.match(floatValues) && !isNaN(value)) {
                return true;
            }
        }, "Anna numeerinen arvo");

        $("#osioform").on('submit',(function(e) {
            e.preventDefault();

            var validform = $('form[id="osioform"]').valid();

            if(validform )
            {
                $.ajax({
                    url: "ajaxosioi.php",
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
                        if(data=='0')
                        {
                            // invalid file format.
                            $("#err").html("Invalid action !").fadeIn();
                        }
                        else
                        {
                            // view uploaded file.
                            $("#osioform").trigger("reset");
                            $('#close-osioi-modal').click();
                        }

                    },
                    error: function(e)
                    {
                        $("#err").html(e).fadeIn();
                    }
                });
            }
        }));

        $('form[id="osioform"]').validate({
            rules: {
                osioname: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                osionsumma: {
                    required: true,
                    currency: true
                }
            },
            messages: {
                osioname:
                    {
                        required: "Osion nimi puuttuu",
                        minlength: "Osion nimi min pituus 3 merkkiä",
                        maxlength: "Osion nimi max pituus 100 merkkiä"

                    },
                osionsumma:
                    {
                        required: "Osion summa puuttuu",
                        digits: "Vain numeerisia arvoja"

                    }
            },
            success: function (label, element) {
                $("#osioibutton").addClass('btn-success').removeClass('btn-secondary');
            }
        });

    });



</script>


<!-- Modal -->
<div class="modal" tabindex="-1" id="osioimodal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lisää osio tilitapahtumaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">


                <p>
                <span id="stype" style="font-weight: bold;">SAAJA:</span>    <span id="debtor"></span> <br/>
                    <span style="font-weight: bold;">SUMMA:</span>    <span class="tamount"></span> <br/>

                </p>

                <div id="err"></div>


                <form id="osioform" action="#" method="post" enctype="multipart/form-data">



                    <input type="hidden" name="tid" id="tid" value=""/>
                    <input type="hidden" name="transid" id="transid" value=""/>

                    <div class="form-group">
                        <label for="osioname">Osion nimi:</label>
                        <input type="text" name="osioname"  id="osioname"  class="form-control"/>

                    </div>
                    <div class="form-group">
                        <label for="selite">Osion summa:</label>
                        <input type="text" name="osionsumma"  id="osionsumma"  class="form-control currency" style="max-width: 150px;"/>

                    </div>

                    <div class="form-group">
                        <button name="submit" type="submit" id="osioibutton" class="btn btn-secondary">Lisää osio</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-osioi-modal">Sulje</button>
            </div>
        </div>
    </div>
</div>

