<?php
session_start();
require_once ('config.php');
$data = getUserTransFull($_SESSION['uid']);
//echo sizeof($data);
?>
<div id="showtrans" style="">

<script>

    //thismonth
    $(document).ready(function () {

        $('.thismonth').click(function() {

        });

    });
</script>


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
            $monbutton = $do->format('m-y');
            $mo = $do->format('m');;
            $ye = $do->format('y');

            $difan = "";
            if($i > 3)
            {
                $difan = "mobileview";
            }
            ?>
            <button type="button" class="btn btn-outline-success monthbutton thismonth <?php echo($difan) ?>" style="margin-right: 10px;" data-month="<?php echo($mo)?>" data-year="<?php echo($ye)?>"><?php echo($monbutton)?></button>


            <?php

        }


        ?>
        <!--
        <button type="button" class="btn btn-outline-success" data-month=""><?php echo($thismonth)?></button>
        <button type="button" class="btn btn-outline-success" data-month=""><?php echo($prevmonth)?></button>
        <button type="button" class="btn btn-outline-success" data-month=""><?php echo($prevmonth2)?></button>
        -->

    </div>

    <table style="width: auto;" class="table table-bordered table-striped table-responsive">
        <caption></caption>
        <thead thead-dark>
        <tr>
            <th scope="col">PVM</th>
            <th scope="col">TAPAHTUMA</th>
            <th scope="col">SUMMA</th>
            <th scope="col">KUITTI</th>
        </tr>
        </thead>
        <tbody>
        <?php
        //var_dump($data);
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

            if (empty(trim($explain)))
            {
                $explain = "SIIRTO";
            }
            //TRANSFER
        }

        $amount = $data[$i]['amount'];

        $status = trim($data[$i]['status']);

        $selite =  $explain." ".$amount;


        $tdate = date_create( $data[$i]['bookingDate']);
        $boodate = date_format($tdate,"j.n");


        ?>
        <tr>
            <th scope="row"><?php echo($boodate) ?></th>
            <td><?php echo($explain) ?></td>
            <td style="text-align: right;"><?php echo($amount) ?></td>
            <td style="text-align:center;">
                <?php

                if ($status == '1')
                {
                    ?>
                    <a data-toggle="modal" data-id="ISBN-001122" data-selite="<?php echo($selite) ?>" title="Add this item" class="add-selite" href="#loadimage"><span class="oi smallico" data-glyph="plus"></span></a>

                    <?php
                }
                else
                {
                    ?>
                    <a data-toggle="modal" data-id="ISBN-001122" title="Add this item" class="add-selite" href="#loadimage"><span class="oi smallico" data-glyph="check"></span></a>
                    <?php
                }

                ?>


            </td>
            <?php
            }
            ?>


        </tr>
        </tbody>
    </table>
</div>
