<?php
session_start();
if(empty($_SESSION['uid']))
{
    header('Location: ./index.php?logout=true');
}




include('config.php');
include('header.php');

?>
<script>
    $(document).ready(function (e) {

        $("#form").on('submit',(function(e) {
            e.preventDefault();
            $.ajax({
                url: "ajaxtiedosto.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    //$("#preview").fadeOut();
                    $("#err").fadeOut();
                },
                success: function (data) {
                    if (data == 'invalid') {
                        // invalid file format.
                        $("#err").html("Virhetilanne !").fadeIn();
                    } else {
                        // view uploaded file.
                        // $("#preview").html(data).fadeIn();
                        $("#form")[0].reset();
                        listaa();
                    }
                },
                error: function (e) {
                    $("#err").html(e).fadeIn();
                }

            });
        }));



        function listaa()
        {
                $.ajax({
                    url: "gettiedostot.php",
                    type: 'POST',
                    dataType: "json",
                    'success' : function(data) {
                        console.log(data);
                        if ( data)
                        {
                            console.log("Jep...");
                            let type = '';
                            let file = '';
                            let aika = '';


                            $("#filelist tr").remove();

                            let thead = "<tr><th>TYYPPI</th><th>NIMI</th></tr>";
                            $("#filelist").html(thead);

                            var table = document.getElementById("filelist");


                            $.each(data, function(index, element) {
                                console.log("Jep...A"+element[1]);

                                file = element[0];
                                aika = element[1];
                                ext = element[2];
                                tid = element[3];


                                var row = table.insertRow(-1);

                                var cell1 = row.insertCell(0);
                                var cell2 = row.insertCell(1);

                                let filu = "<a  href='loadtiedosto.php?tid="+tid+"'>"+file + "</a><br><i style='font-size:0.8em;'>"+aika+"</i>"


                                let exter = "<img style='width:35px;' src='./img/icon-"+ext+".png' alt='icon'>";

                                cell1.innerHTML = exter;
                                cell2.innerHTML = filu;

                            });
                    }
                }

        });
        }

        $('#addfilebtn').click(function(e) {
           e.stopPropagation();
            $('#addfiles').show();
        });

        listaa();
    });
</script>

<div class="container">
    <div class="row">

        <div class="col-md-8">

            <h2>Tiedostot</h2>


            <button type="button" id="addfilebtn"  class="btn btn-outline-success"><i class="fa fa-plus" style="font-size:1.1em;" ></i> Lisää</button>
            <br/>   <br/>

            <div id="addfiles" style="display: none;">
                <h3>Lisää tiedosto</h3>
                <form id="form" action="ajaxupload.php" method="post" enctype="multipart/form-data">

                    <input id="uploadImage" type="file" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*" name="image" />
                    <br><br/>
                    <input class="btn btn-success" type="submit" value="Tallenna">
                </form>

                <div id="err"></div>

                <hr/>
            </div>

            <table id="filelist">


            </table>



        </div>
    </div>
</div>



<?php
include('footer.php');

?>
