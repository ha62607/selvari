<?php session_start()?>
<?php
require_once ('config.php');
$loadpinger = true;

$init_trans = false;

if (!empty($_GET['ref']))
{
    $bankref = set_safe($_GET['ref']);

    if (strlen($bankref) > 10)
    {
        //echo($bankref);
        $_SESSION['acref'] = $bankref;
        $bankid = $bankref;
        saveBank($_SESSION['uid'], $_SESSION['bankname'], $bankref, $bankid);
        $init_trans= true;
    }

}

?>
<?php include('./header.php')?>

<?php //echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>'; ?>



<script>
    $(document).ready(function () {



    function renderTrans()
    {


        console.log("List transactions...");

        $.ajax({

            'url' : './showtrans2.php',
            'type' : 'POST',
            'data' : {
                'bank' : '1'
            },
            'success' : function(data) {
                //console.log('Data: '+data);
                $('#show-trans-block').show();
                $('#show-trans-block').html(data);
                $('#choosebank').hide();

            },
            'error' : function(request,error)
            {
                alert("Request: "+JSON.stringify(request));
            }
        });
    }


       let gobank = '';

    function setBank(banklink)
    {

        $('#open-bank-modal').modal('show');

    }

        $('#open-bank-connection').on("click", function()
            {
                window.location.href = gobank;
                console.log("Siirtyy pankkiin ....");
                return false;
            }

        );

    $('.bankverify').click(function() {

        let bank = $(this).attr('id');
        console.log("ID:"+bank);

        $.ajax({

            'url' : './rsa/getbanklink.php',
            'type' : 'GET',
            'data' : {
                'bank' : bank
            },
            'success' : function(data) {
                console.log('Data: '+data);
                gobank = data;
                setBank(data);
            },
            'error' : function(request,error)
            {
                alert("Request: "+JSON.stringify(request));
            }
        });


        });

        function dotrans()
        {


            console.log("Retreiving transactions...:");

            $.ajax({

                'url' : './setbanktrans.php',
                'type' : 'GET',
                'success' : function(data) {
                    console.log('Data: '+data);
                    if(data == '1')
                    {
                        $('#spinner').hide();
                        $('#dbdone').show();
                        $('#init-trans').fadeOut(3000,function() {
                            $('#init-trans').modal('hide');
                            //choosebank
                            $('#choosebank').hide();
                            $('#showtrans').show();
                            renderTrans();
                        });
                    }

                },
                'error' : function(request,error)
                {
                    //alert("Request: "+JSON.stringify(request));
                }
            });



        }


    <?php
        if ($init_trans)
        {
        ?>
        $('#init-trans').modal('show');
        $('#dbdone').hide();
        dotrans();
        <?php
        }

        ?>

        <?php
        $data = getUserTransFull($_SESSION['uid']);
        if(sizeof($data) > 0)
        {
        ?>

        $('#showtrans').show();
        renderTrans();


        <?php
        }

        ?>




    });
</script>

<main class="container">


<?php require_once('./modal-add-kuitti.php') ?>

    <?php require_once('./modal-open-bank.php') ?>


    <?php require_once('./modal-init-trans.php') ?>

    <?php require_once('./modal-show-file.php') ?>

    <?php require_once('./modal-add-selite.php') ?>

    <?php require_once('./modal-tapahtuma.php') ?>

    <?php require_once('./modal-tili.php') ?>

    <?php require_once('./modal-osioi.php') ?>

    <div id="buttonDiv">
    </div>


    <div class="bg-light p-2 rounded">



        <div id="show-trans-block">


        </div>




        <div id="choosebank">
        <h2>Valitse pankkisi</h2>
        <p>
            Valitse oman yrityksesi pankki klikaamalla pankin ikonia.
        </p>

        <div class="container text-center">
            <div class="row g-5 text-center">
                <div class="col">
                    <img src="./img/banks/danske.png" alt="Danske Bank" class="banklistimg border bankverify" id="danske"/>
                </div>
                <div class="col">
                    <img src="./img/banks/handelsbanken.png" alt="Handelsbanken" class="banklistimg border bankverify"  id="handel"/>
                </div>
                <div class="col">
                    <img src="./img/banks/nordea.png" alt="Nordea" class="banklistimg border bankverify"  id="nordea"/>
                </div>
            </div>
            <div class="row  g-5 text-center" >
                <div class="col">
                    <img src="./img/banks/omasp.png" alt="Oma SP" class="banklistimg border bankverify"  id="omasp"/>
                </div>

                <div class="col">
                    <img src="./img/banks/op.png" alt="Osuuspankki" class="banklistimg border bankverify"  id="op"/>
                </div>
                <div class="col">
                    <img src="./img/banks/pop.png" alt="POP pankki" class="banklistimg border bankverify"  id="pop"/>
                </div>
            </div>

            <div class="row  g-5 text-center">
                <div class="col">
                    <img src="./img/banks/saastopankki.png" alt="Säästöpankki" class="banklistimg border bankverify"  id="saasto"/>
                </div>
                <div class="col">
                    <img src="./img/banks/spankki.png" alt="S-Pankki" class="banklistimg border bankverify"  id="spankki"/>
                </div>
                <div class="col">
                    <img src="./img/banks/alands.png" alt="Ålands Banken" class="banklistimg border bankverify"  id="alands"/>
                </div>
            </div>
        </div>
        </div>





    </div>

    <script>
        $(document).ready(function () {

            $('#vientiblock').click(function(e){

                e.stopPropagation();

                return false;
            });

        });
    </script>



    <div id="vientiblock">

        <a href="#">Avaa vienti&rsaquo;</a>
        <br/><br/>
        <div class="viennit">
            <a href="#" id="fivaldi">Generoi fivaldi &rsaquo;</a>
        </div>

    </div>

</main>


<?php include('./footer.php')?>
