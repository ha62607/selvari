<?php
session_start();
require_once ('config.php');

$mon = '0';
$year = '0';


if (!empty($_POST['month']) && !empty($_POST['year']))
{
    $mon_test = ltrim($_POST['month'], "0");
    $year_test = ltrim($_POST['year'], "0");

    if (!empty($mon_test ) && is_numeric($mon) && !empty($year_test ) && is_numeric($year))
    {
        $mon = $mon_test;
        $year = $year_test;

    }

}


error_log("M:".$mon." Y:".$year);

$data = getUserTransFull($_SESSION['uid'],$mon,$year);
//echo sizeof($data);

?>
<div id="showtrans" style="">

<script>

    //thismonth
    $(document).ready(function () {

        var month = 0;
        var year = 0;





        function renderTransMonth()
        {
            console.log("List transactions monthly...");

            $.ajax({

                'url' : './showtrans2.php',
                'type' : 'POST',
                'data' : {
                    'month' : month,
                    'year'  : year
                },
                'success' : function(data) {
                    //console.log('Data: '+data);
                    $('#show-trans-block').show();
                    $('#show-trans-block').html(data);

                },
                'error' : function(request,error)
                {
                    alert("Request: "+JSON.stringify(request));
                }
            });

        }


        function showkuitit(kid,type,tid,paikka,modal)
        {

            var ele = "#kid_"+kid;
            var test = $("#kuitti_"+tid).find(ele).length;


            if(!test || modal)
            {
                if (type == 'pdf')
                {
                    if(!modal) {
                        $('<a href="javascript:nogo();"><img  src="./img/pdf.png" alt="" id="kid_' + kid + '" class="kuittielement thumbpdf" /></a>').appendTo('#kuitti_' + tid);
                    }
                    else
                    {
                        var p = 1;
                        if(parseInt(paikka) == 2)
                        {
                            p = 2;
                        }

                        $('#kuvaform'+p).hide();
                        $('#preview'+p).hide();
                        $('#kuvapaikka'+p).html("");
                        $('<img  src="./img/pdf.png" alt="" id="kid_' + kid + '" class="kuittielement modalpdf" />').appendTo('#kuvapaikka' + p);


                    }
                }
                else
                {
                    if(!modal) {
                        $('<a href="javascript:nogo();"><img class="thumbkuitti" src="./getfile.php?kuitti=' + kid + '" alt="" id="kid_' + kid + '" class="kuittielement" /></a>').appendTo('#kuitti_' + tid);
                    }
                    else
                    {
                        var p = 1;
                        if(parseInt(paikka) == 2)
                        {
                            p = 2;
                        }

                        $('#kuvaform'+p).hide();
                        $('#preview'+p).hide();
                        $('#kuvapaikka'+p).html("");
                        $('<img src="./getfile.php?kuitti=' + kid + '" alt="" id="kid_' + kid + '" class="kuittielement modalkuitti" />').appendTo('#kuvapaikka' + p);
                    }
                }
            }


        }


        function kuittihandle(tid,modal)
        {
            console.log("Kuittihandle:"+tid);
            $.ajax({

                'url' : './getkuitti.php',
                'type' : 'POST',
                dataType: "json",
                'data' : {
                    'tapahtuma' : tid
                },
                'success' : function(data) {
                    console.log('Data: '+data);
                    if(!data)
                    {
                        console.log("Ei kuittia");
                        $('#kuvapaikka1').html("");
                        $('#kuvaform1').show();
                        $('#preview1').show();

                        $('#kuvapaikka2').html("");
                        $('#kuvaform2').show();
                        $('#preview2').show();

                    }
                    else if (data[0])
                    {
                        var kbtn = ".kbtn_"+tid;
                        $(kbtn).text("Muokkaa kuittia");
                        var ar1 = data[0].split(":");
                        showkuitit(ar1[0],ar1[1],tid,1,modal)

                        if (data[0] && !data[1])
                        {
                            $('#kuvapaikka2').html("");
                            $('#kuvaform2').show();
                            $('#preview2').show();

                        }

                        if (data[1]) {
                            var ar2 = data[1].split(":");

                            showkuitit(ar2[0],ar2[1],tid,2,modal)

                        }
                    }

                },
                'error' : function(request,error)
                {
                    //alert("Request: "+JSON.stringify(request));
                }
            });


        }

        function render_load_modal(tid,modal)
        {
            kuittihandle(tid,modal);
        }


        $('.accordion-item').click(function(e) {

                var tid = $(this).data('tapahtuma');
                console.log("TAP:"+tid);
                kuittihandle(tid,false);

        });

        $('.thismonth').click(function() {

        });

        $('.add-selite').click(function(e){

            e.stopPropagation();
            console.log("Jep jep...");

            var tid = $(this).data('tid');
            var transId = $(this).data('transid');
            $("#tid").val(tid);
            $("#transid").val(transId);


            var selite = $(this).data('selite');
            //selite = selite.replace(/(\r\n|\n|\r)/gm, "");
            var amount = $(this).data('amount');
            console.log("Hop hop:"+transId);
            $(".modal-body #lataaid").text( selite+" "+amount);
            $(".modal-body #kuitti #tid").val(tid);
            // As pointed out in comments,
            // it is unnecessary to have to manually call the modal.
            render_load_modal(tid,true);
            $('#loadimage').modal('show');

        });


        $('.showkuitti').click(function(){
            console.log("Show kuitti...");

            var tid = $(this).data('tid');
            var kid= $(this).data('kid');


            var selite = $(this).data('selite');
            //selite = selite.replace(/(\r\n|\n|\r)/gm, "");
            var amount = $(this).data('amount');
            console.log("Hop hop:"+transId);
            $(".modal-body #lataaid").text( selite+" "+amount);
            // As pointed out in comments,
            // it is unnecessary to have to manually call the modal.
            $('#loadimage').modal('show');

        });



        $('.monthrow .monthbutton').click(function(){
            month = $(this).data('month');
            year = $(this).data('year');
            renderTransMonth();
            //console.log("MONTH:"+month);
            //console.log("YEAR:"+year);

        });





    });
</script>
<style>
    .accordion-button:not(.collapsed) {
        background-color: #368656;
        color: #FFF;

    }

    .accordion-button:focus {
        z-index: 3;
        border-color: #000 !important;
        outline: 0;
        box-shadow: 0 0 0 .05rem #000 !important;
    }
    .accordion-button:focus::after {
        color:white !important;

    }

    .accordion-button:focus::after {background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e"); }




</style>



    <div style="" class="monthrow">
        <?php
        /*
        $thismonth = date("m-Y");

        $oneMonthAgo = new \DateTime('1 month ago');
        $prevmonth = $oneMonthAgo->format('m-Y');

        $twoMonthAgo = new \DateTime('2 month ago');
        $prevmonth2 = $twoMonthAgo->format('m-Y');
        */

        for ($i = 0; $i < 12; $i++)
        {
            $do = new \DateTime($i.' month ago');
            $monbutton = $do->format('m/y');
            $mo = $do->format('m');;
            $ye = $do->format('y');

            $difan = "";
            if($i > 2)
            {
                $difan = "mobileview";
            }
            ?>
            <button type="button" class="btn btn-outline-success monthbutton thismonth <?php echo($difan) ?>" style="margin-right: 10px;" data-month="<?php echo($mo)?>" data-year="<?php echo($ye)?>"><?php echo($monbutton)?></button>


            <?php

        }


        ?>


    </div>




    <div class="accordion accordion-flush" id="accordionExample">

        <?php
        //var_dump($data);
        //
        for ($i = 0; $i < sizeof($data); $i++)
        {
        $explain = $data[$i]['creditorName'];
        if (empty($explain))
        {
            $explain = $data[$i]['debtorName'];
        }
        if (empty($explain))
        {
            $explain = $data[$i]['remittanceInformationUnstructured'];
            $explain = str_replace("EndToEndID: NOTPROVIDED","",$explain);
            $explain = str_replace("TRANSFER","",$explain);

            $trimmed = trim($explain);
            if (empty($trimmed))
            {
                $explain = "SIIRTO";
            }
            //TRANSFER
        }

        $small = substr($explain,0,22);

        $amount = $data[$i]['amount'];

        $tid = $data[$i]['tid'];
        $transid = $data[$i]['transactionId'];


        $color = "red";

        if ( intval($amount) > 0)
        {
            $color = "green";

        }

        $status = trim($data[$i]['status']);

        $selite =  $explain." ".$amount;


        $tdate = date_create( $data[$i]['bookingDate']);
        $boodate = date_format($tdate,"j.n");

        $acval = 'gone'.$i;

        $show = "";
        $expan = "false";
        if ($i == 0)
        {
            //$expan = "true";
            //$show  = "show";

        }
        $kuitti = set_safe($explain);
        $kuitti = str_replace("\\n"," ",$kuitti);
            $kbtn =  "kbtn_".$tid;
        ?>

        <div class="accordion-item" data-tapahtuma="<?php echo($tid)?>"  data-state="0">
            <h2 class="accordion-header" id="heading_<?php echo($acval)?>">
                <button class="accordion-button collapsed dooper" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?php echo($acval)?>" aria-expanded="<?php echo($expan)?>" aria-controls="collapse_<?php echo($acval)?>">
                    <div class="amount"><?php echo($boodate)?></div> <div class="amount <?php echo($color) ?>"><?php echo($amount)?> &euro;</div>   <span class="desktop"><?php echo($explain)?></span> <span class="mobile"><?php echo($small )?></span>
                </button>
            </h2>
            <div id="collapse_<?php echo($acval)?>" class="accordion-collapse collapse <?php echo($show)?>" aria-labelledby="heading_<?php echo($acval)?>" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <?php
                   if ($color == 'green')
                    {
                       ?>
                        MAKSU: <?php echo($explain) ?>

                   <?php
                    }
                    else
                    {
                        ?>
                    LASKU: <?php echo($explain) ?>
                    <?php
                    }
                    ?>
                    <br/><br/>
                    <div class="container" style="">
                        <div class="row" style="">
                            <div class="col" style="float:left;">

                                <div id="kuitti_<?php echo($tid)?>">

                                </div>
                                <div style="clear:both;"></div>


                                <button type="button"  data-tid="<?php echo($tid) ?>" data-transid="<?php echo($transid) ?>" data-selite="<?php echo($kuitti) ?>" data-amount="<?php echo($amount) ?>" class="btn btn-outline-success add-selite <?php echo($kbtn) ?>">Lisää kuitti</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    <?php
    }
    ?>

    </div>

</div>
