<script>
    $(document).ready(function (e) {





        $("#kuitti").on('submit',(function(e) {
            e.preventDefault();

            $.ajax({
                url: "ajaxupload.php",
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
                        $("#preview1").html(data).fadeIn();
                        //$("#kuitti")[0].reset();
                    }

                    console.log("IMG UPLOAD:"+data);
                },
                error: function(e)
                {
                    $("#err").html(e).fadeIn();
                }
            });
        }));
    });
</script>
<script>

    var loadFile1 = function(event) {


        var file1 =  $("#kuva1")[0].files[0].size;
        if(file1 > 15000000)
        {
            document.getElementById('kuva1err').innerHTML = "<span class='error'>Liian suuri tiedosto.</span><br/>";
            return;
        }
        else
        {
            document.getElementById('kuva1err').innerHTML = "";

        }

        var image = document.getElementById('img-upload-1');
        var extension = event.target.files[0].type
        if (extension == 'application/pdf')
        {
            image.src = './img/pdf.png';
        }
        else
        {
            image.src = URL.createObjectURL(event.target.files[0]);
        }

    };

    var loadFile2= function(event) {

        var file2 =  $("#kuva2")[0].files[0].size;
        if(file2 > 15000000)
        {
            document.getElementById('kuva2err').innerHTML = "<span class='error'>Liian suuri tiedosto.</span><br/>";
            return;
        }
        else
        {
            document.getElementById('kuva2err').innerHTML = "";

        }



        var image = document.getElementById('img-upload-2');
        var extension = event.target.files[0].type
        if (extension == 'application/pdf')
        {
            image.src = './img/pdf.png';
        }
        else
        {
            image.src = URL.createObjectURL(event.target.files[0]);
        }
    };

    var loadFile3 = function(event) {
        var image = document.getElementById('img-upload-3');
        var extension = event.target.files[0].type
        if (extension == 'application/pdf')
        {
            image.src = './img/pdf.png';
        }
        else
        {
            image.src = URL.createObjectURL(event.target.files[0]);
        }
    };

</script>

<!-- Modal -->
<div class="modal" tabindex="-1" id="loadimage">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lisää kuitti tilitapahtumasta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <p><span id="lataaid"></span> &euro;</p>

                <div id="err"></div>


                <form id="kuitti" action="ajaxupload.php" method="post" enctype="multipart/form-data">

                    <input type="text" id="tid" name="tid" value=""/>

                    <input type="hidden" name="tid" id="tid" value=""/>
                    <input type="hidden" name="transid" id="transid" value=""/>

                    <div class="form-group">
                        <label for="kuva1">Kuva tai pdf-tiedosto 1</label>

                        <div id="kuvapaikka1"></div>
                            <div class="input-group" id="kuvaform1">
                                <input id="kuva1" name="kuva1" placeholder="Kuva tai .pdf-tiedosto" type="file" class="form-control" accept="application/pdf,image/*"  onchange="loadFile1(event)" >
                            </div>
                            <div id="preview1"><span id="kuva1err"></span><img src="./img/empty.png" id="img-upload-1" /></div>

                    </div>

                    <div class="form-group">
                        <label for="kuva2">Kuva tai pdf-tiedosto 2</label>

                        <div id="kuvapaikka2"></div>
                            <div class="input-group" id="kuvaform2">
                                <input id="kuva2" name="kuva2" placeholder="Kuva tai .pdf-tiedosto" type="file" class="form-control" accept="application/pdf,image/*"  onchange="loadFile2(event)" >
                            </div>
                            <div id="preview2"><span id="kuva2err"></span><img src="./img/empty.png" id="img-upload-2" /></div>

                    </div>

                    <div class="form-group">
                        <label for="selite">Selite </label>
                        <textarea id="selite" name="selite" cols="50" rows="3" aria-describedby="seliteHelpBlock" class="form-control"></textarea>
                        <i style="font-size:0.9em;">*max 1000 merkkiä</i>
                    </div>
                    <div class="form-group">
                        <button name="submit" type="submit" class="btn btn-secondary">Lähetä</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulje</button>
            </div>
        </div>
    </div>
</div>

