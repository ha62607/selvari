<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require_once ('config.php');

$mon = '0';
$year = '0';

$tulosdata = false;

$aliasenabled = false;



if (!empty($_SESSION['alias']) && $_SESSION['alias'] === 'true' ) {

    $aliasenabled = true;

}

if (!empty($_POST['month']) && !empty($_POST['year']))
{
    $mon_test = ltrim($_POST['month'], "0");
    $year_test = ltrim($_POST['year'], "0");

    if (!empty($mon_test ) && is_numeric($mon) && !empty($year_test ) && is_numeric($year))
    {
        $tulosdata = true;

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

    var loadmock = false;

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

                    $('#show-trans-block').show();
                    $('#show-trans-block').html(data);

                    $('#smon').val(month);
                    $('#syear').val(year);

                    $('#vientiblock').show();

                },
                'error' : function(request,error)
                {

                    alert("Request: "+JSON.stringify(request));
                }
            });

        }



        function renderTapahtuma(tid)
        {
            console.log("List single transaction ...");

            $.ajax({

                'url' : './gettrans.php',
                'type' : 'POST',
                'data' : {
                    'tid' : tid
                },
                'success' : function(data) {
                    console.log('Data: '+data);
                    //$('#show-trans-block').show();
                    //$('#show-trans-block').html(data);

                },
                'error' : function(request,error)
                {
                    alert("Request: "+JSON.stringify(request));
                }
            });

        }

        function showkuitit(data,tid)
        {



            var kuittidata = data.toString();

            //console.log("KUID:"+type);
            if(kuittidata.length > 4)
            {
                kuittidata = kuittidata.split(":");
                var kuitti = kuittidata[0];
                var type = kuittidata[1];

                //console.log("K:"+kuitti);
                //console.log("T:"+type);

                if ( type == 'pdf')
                {
                    return  '<a href="javascript:nogo();"><img  src="./img/pdf.png" alt="" id="kid_' + kuitti + '" data-id="' + kuitti + '" data-tid="'+tid+'"  class="kuittielement modalpdf thumbpdf" /></a>';
                }
                else
                {
                    return  '<a href="javascript:nogo();" class="kuitti_img"><img src="./getfile.php?kuitti=' + kuitti + '" alt="" data-id="' + kuitti + '" data-tid="'+tid+'" class="kuittielement modalkuitti thumbkuitti" /></a>';
                }

            }


        }

        function kuittistatus(tid)
        {
            $.ajax({

                'url' : './kuittistatus.php',
                'type' : 'POST',
                'data' : {
                    'tid' : tid
                },
                'success' : function(data) {
                    //console.log('Data: '+data);


                    data = data.trim();
                    var st = parseInt(data);
                    if(st == 0)
                    {
                        $('#dot_'+tid).removeClass("greendot");
                        $('#dot_'+tid).addClass("defaultdot");
                        $('#kuitti_'+tid).html("");


                    }
                    else
                    {
                        $('#dot_'+tid).removeClass("defaultdot");
                        $('#dot_'+tid).addClass("greendot");
                    }
                },
                'error' : function(request,error)
                {
                }
            });
        }

        function kuittihandle(tid)
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
                    //console.log('Data: '+data);
                    if(!data)
                    {
                        console.log("Ei kuittia");
                        kuittistatus(tid);

                    }
                    else if (data)
                    {
                        console.log("Juu");
                        var json = $.parseJSON(JSON.stringify(data));
                        //console.log(json);

                        $('kuitti_'+tid).html("");
                        let kuittipalat = "";
                        for(let i = 0; i < json.data.length; i++)
                        {
                            console.log("Jaa");
                            kuittipalat = kuittipalat  + showkuitit(json.data[i],tid);
                            kuittistatus(tid);
                        }
                        $('#kuitti_'+tid).html(kuittipalat);
                    }

                },
                'error' : function(request,error)
                {
                    //alert("Request: "+JSON.stringify(request));
                }
            });
        }


        function tilihandle(tid) {
            console.log("Tilihandle:" + tid);

            $.ajax({

                'url' : './gettili.php',
                'type' : 'POST',
                dataType: "json",
                'data' : {
                    'tapahtuma' : tid
                },
                'success' : function(data) {
                   // console.log('Data: '+data);
                    if(!data)
                    {
                       // console.log("Ei tiliä");
                    }
                    else if (data)
                    {


                      //  console.log("Juu on tili");
                        var json = $.parseJSON(JSON.stringify(data));
                        //console.log(json);

                        let nimi = '';
                        let koodi = '';
                        let alv = '';
                        $.each(data, function(index, element) {

                            if (index == 0) koodi = element;
                            else if (index == 1) nimi = element;
                            else if (index == 2) alv = element;

                       //     console.log(index +":" +element);
                        });

                        if(koodi)
                        {
                            $('#tilinimi_'+tid).text(nimi);
                            $('#tilikoodi_'+tid).text(koodi);
                            $('#tilialv_'+tid).text(alv);
                            $('#rec_'+tid).removeClass("defaulrec");
                            $('#rec_'+tid).addClass("greenrec");

                        }

                    }

                },
                'error' : function(request,error)
                {
                    //alert("Request: "+JSON.stringify(request));
                }
            });

        }

        function gettilidata(tilidata,sid,koodi)
        {
            let value = '';

            //console.log("ABC:"+tilidata);
            $.each(tilidata, function(index, element) {
                //console.log("EDF:"+element);
                let esid = element[0];
                $.each(element, function(sindex, subelement) {
                    //console.log("TILI:"+subelement+ " IND:"+sindex+" KOODI:"+koodi+" SID:"+element[0]);
                    if (esid == sid)
                    {
                        if(koodi.trim() == "koodi" && sindex == 1)
                        {
                            value = subelement;
                        }
                        else if ( koodi.trim() == 'nimi' && sindex == 2)
                        {
                            value = subelement;
                        }
                        else if ( koodi.trim() == 'sid' && sindex == 0)
                        {
                            value = subelement;
                        }
                        else if ( koodi.trim() == 'alv' && sindex == 3)
                        {
                            value = subelement;
                        }
                    }
                });

            });

            return value;
        }


        function showosiot(tid,tapsumma)
        {
            console.log("Show osiot:" + tid);

            let tapahtumansumma = $('#tapahtuma_'+tid).data('summa');
            tapsumma = parseFloat(tapahtumansumma);


            //$("#subtilit_"+tid+" tr").remove();
            $("#subtilitholder_"+tid).html('');

            $.ajax({

                'url' : './getosiot.php',
                'type' : 'POST',
                dataType: "json",
                'data' : {
                    'tapahtuma' : tid
                },
                'success' : function(data) {
                    console.log('Osio data: '+data);
                    if(!data)
                    {
                       console.log("Ei osioita");
                        $("#maintili_"+tid).show();
                        $("#maintilibtn_"+tid).show();
                    }
                    else if (data)
                    {
                        let count = 0;

                        console.log("Juu on osioita");

                        let totalsum = 0;
                        let tilidata = '';
                        $.each(data, function(index, element) {
                            //console.log("Osiodata: "+index +":" +element);

                            count++;
                            if(count == 1)
                            {
                                console.log("Adding table....");
                                let table = '<table style="width:100%; margin-top:15px;" class="subtilirows" id="subtilit_'+tid+'"></table>';
                                console.log(table);
                                $("#subtilitholder_"+tid).html(table);

                                let head = '<tr><th>OSIONIMI</th><th>OSIOSUMMA</th><th>TILINIMI</th><th>TILIKOODI</th><th  class="alvbox">ALV %</th><th>ASETA TILI</th><th>MUOKKAA</th><th>POISTA</th></tr>';
                                $("#subtilit_"+tid).append(head);
                                $("#maintili_"+tid).hide();
                                $("#maintilibtn_"+tid).hide();

                            }
                            if(count == 1)
                            {

                                $.ajax({
                                    url: "getsubtilit.php",
                                    type: 'POST',
                                    dataType: "json",
                                    'data' : {
                                        'tid' : tid
                                    },
                                    async: false,
                                    cache: false,
                                    timeout: 10000,
                                    'success' : function(data) {
                                        console.log("Data tilikoodi: " + data + "\nStatus: " + status);
                                        console.log("D:"+data);
                                        tilidata = data;
                                    }
                                });
                            }


                            let sid = "";
                            let name = "";
                            let summa = "";
                            let idt = "";

                                $.each(element, function(sindex, subelement) {
                                //console.log("Osiorivi: "+sindex +":" +subelement);
                                if (sindex == 0) sid = subelement;
                                else if (sindex == 1) name = subelement;
                                else if (sindex == 2) summa = subelement;

                            });




                            //subtilit

                            var table = document.getElementById("subtilit_"+tid);


                            var row = table.insertRow(-1);
                            var cell1 = row.insertCell(0);
                            var cell2 = row.insertCell(1);
                            var cell3 = row.insertCell(2);
                            var cell4 = row.insertCell(3);
                            var cell5 = row.insertCell(4);
                            var cell6 = row.insertCell(5);
                            var cell7 = row.insertCell(6);
                            var cell8 = row.insertCell(7);

                            let sa = parseFloat(summa);
                            totalsum = totalsum +sa;

                            let koodi = gettilidata(tilidata,sid,'koodi');
                            let nimi= gettilidata(tilidata,sid,'nimi');
                            if (nimi && nimi.length > 20)
                            {
                                nimi =  nimi.substring(0,20) + "...";

                            }
                            let alv = gettilidata(tilidata,sid,'alv');

                            if (alv) alv = alv + " %";

                           // console.log("KOODI ON:"+koodi);
                            cell1.innerHTML = name;
                            cell2.innerHTML = sa.toFixed(2);
                            cell3.innerHTML = '<span id="subtilikoodi_'+sid+'">'+nimi+'</span>';
                            cell4.innerHTML = '<span id="subtilinimi_'+sid+'">'+koodi+'</span>';
                            cell5.innerHTML = '<span class="alvbox" id="subtilialv_'+sid+'">'+alv+'</span>';
                            cell6.innerHTML = "<button type='button' class='btn btn-outline-success subaseta tili' data-tid='"+tid+"' data-sid='"+sid+"' data-selite='"+name+"' data-amount='"+sa+"' data-sa='"+sa+"'>Aseta</button>";
                            cell7.innerHTML = "<button type='button' class='btn btn-outline-success subedit' data-tid='"+tid+"' data-sid='"+sid+"'  data-summa='"+tapsumma+"' data-sa='"+sa+"'>Muokkaa</button>";
                            cell8.innerHTML = "<button type='button' class='btn btn-outline-success subremove' data-tid='"+tid+"' data-sid='"+sid+"' data-selite='"+name+"' data-amount='"+tapsumma+"' data-sa='"+sa+"'>Poista</button>";


                        });



                        if (count > 0)
                        {



                            let orgsumma = Math.abs(parseFloat(tapsumma));
                            let comsumma = Math.abs(totalsum);

                            let tila = 1;
                            let erotus = 0;
                            if (orgsumma < comsumma)
                            {
                                tila = 2;
                                erotus = comsumma - orgsumma;
                            }
                            else if (orgsumma > comsumma)
                            {
                                tila = 3;
                                erotus = orgsumma - comsumma;

                            }

                            var table = document.getElementById("subtilit_"+tid);
                            var row = table.insertRow(-1);
                            var cell1 = row.insertCell(0);
                            var cell2 = row.insertCell(1);
                            var cell3 = row.insertCell(2);
                            var cell4 = row.insertCell(3);
                            var cell5 = row.insertCell(4);
                            var cell6 = row.insertCell(5);
                            var cell7 = row.insertCell(6);

                            cell1.innerHTML = '<b>Total:</b>';
                            cell2.innerHTML = totalsum.toFixed(2);

                            let infor = '<span class="okgreen">täsmää</span>'
                            if (tila == 2)
                            {
                                infor = '<span class="errorred">liikaa: '+erotus.toFixed(2)+'</span>'
                            }
                            else if (tila == 3)
                            {
                                infor = '<span class="errorred">puuttuu: '+erotus.toFixed(2)+'</span>'
                            }

                            cell3.innerHTML =  infor;

                        }
                        else
                        {
                            $("#maintili_"+tid).show();
                            $("#maintilibtn_"+tid).show();
                        }


                    }

                },
                'error' : function(request,error)
                {
                    //alert("Request: "+JSON.stringify(request));
                }
            });


        }


        function render_load_modal(tid)
        {
            kuittihandle(tid);
        }

        $('#loadimage').on('hidden.bs.modal', function (e) {

            var tid = $('#loadimage #tid').val();
            kuittihandle(tid);
        })

        $('#showfile').on('hidden.bs.modal', function (e) {

            var tid = $('#showfile #removetapahtuma').val();
            console.log("TID:"+tid);
            kuittihandle(tid);
        })



        function selitehandle(tid)
        {
            console.log("Selitehandle:"+tid);
            $.ajax({

                'url' : './getselite.php',
                'type' : 'POST',
                'data' : {
                    'tapahtuma' : tid
                },
                'success' : function(data) {
                    console.log('Data: '+data);
                    if(!data)
                    {
                        console.log("Ei selitettä");

                    }
                    else if (data)
                    {
                        let selite = data.toString();
                        console.log("Juu:"+selite);
                        $('#selite_'+tid).text(selite);
                        $('.skbtn_'+tid).text("Päivitä selite");
                        kuittistatus(tid);
                    }

                },
                'error' : function(request,error)
                {
                    alert("Request: "+JSON.stringify(request));
                }
            });
        }


        $('#addselite').on('hidden.bs.modal', function (e) {

            var tid = $('#addselite #tid').val();
            selitehandle(tid);

            //console.log("Juu nääs päivää TID:"+tid);
            // do something...
        })

        $('#showfile').on('hidden.bs.modal', function (e) {

            var tid = $('#showfile #removetapahtuma').val();
            kuittihandle(tid);

            console.log("POISTO Juu nääs päivää TID:"+tid);
            // do something...
        })

        function clickpolice(e)
        {

            if( $(e.target).hasClass('subremove') || $(e.target).hasClass('subaseta') || $(e.target).hasClass('subedit') ||
                $(e.target).hasClass('add-osioi') || $(e.target).hasClass('add-tili') || $(e.target).hasClass('add-selite')
                || $(e.target).hasClass('add-kuitti')  || $(e.target).hasClass('accordion-body')  || $(e.target).hasClass('collapsed') || $(e.target).hasClass('col'))
            {
                return false;
            }

            else
            {
                let classes = $(e.target).prop("classList");

                if (!classes || classes.length === 0)
                {
                    return false;
                }

                return true;
            }

        }


        $('.accordion-item').click(function(e) {

            if( !clickpolice(e) )
            {
                return;
            }


            console.log("CLASSES:"+  $(e.target).prop("classList") );

            var tid = $(this).data('tapahtuma');
            var summa = $(this).data('summa');
            console.log("TAP:"+tid);
            kuittihandle(tid);
            tilihandle(tid);
            showosiot(tid,summa);

        });

        $('.thismonth').click(function() {

        });

        $('.add-kuitti').click(function(e){

            e.stopPropagation();
            console.log("Jep jep...");

            var tid = $(this).data('tid');
            var transId = $(this).data('transid');
            $("#tid").val(tid);
            $("#transid").val(transId);


            var selite = $(this).data('selite');
            var amount = $(this).data('amount');
            console.log("Hop hop 1:"+transId);
            $(".modal-body #lataaid").text( selite+" "+amount);
            $(".modal-body #kuitti #tid").val(tid);
            // As pointed out in comments,
            // it is unnecessary to have to manually call the modal.
            render_load_modal(tid,true);
            $('#loadimage').modal('show');

        });


        $('.add-selite').click(function(e){

            e.stopPropagation();
            console.log("Jep jep...");

            var tid = $(this).data('tid');
            var transId = $(this).data('transid');
            $("#addselite #tid").val(tid);
            $("#addselite #transid").val(transId);


            var selite = $(this).data('selite');
            var amount = $(this).data('amount');
            console.log("Hop hop 1:"+transId);
            $(".modal-body #lataaid").text( selite+" "+amount);
            $('#addselite').modal('show');

        });





        $(document).on('click', '.thumbkuitti', function(e){

            e.stopPropagation();
            console.log("Show kuitti...");
            var tid = $(this).data('tid');
            var kid= $(this).data('id');


            var selite = $(this).data('selite');
            var amount = $(this).data('amount');
            console.log("Hop hop 2:"+tid);
            $(".modal-body #kuittishow").show();
            $(".modal-body #pdfshow").hide();
            $(".modal-body #kuittishow").attr('src',"./getfile.php?kuitti="+kid);
            //removekuitti
            $(".modal-body #removekuitti").val(kid);
            $(".modal-body #removetapahtuma").val(tid);
            $(".modal-body #pdflink").hide();

            $('#showfile').modal('show');
        });



        $(document).on('click', '.thumbpdf', function(e){

            e.stopPropagation();
            console.log("Show kuitti...");
            var tid = $(this).data('tid');
            var kid= $(this).data('id');


            var selite = $(this).data('selite');
            var amount = $(this).data('amount');
            console.log("Hop hop 2:"+tid);
            //removekuitti
            $(".modal-body #removekuitti").val(kid);
            $(".modal-body #removetapahtuma").val(tid);
            //kuittipdfframe
            $(".modal-body #kuittishow").hide();
            $(".modal-body #pdfshow").show();
            var kuittipath = "<?php echo($SITE) ?>getfile.php?kuitti="+kid;
            $(".modal-body #pdflink").css('display','inline-block');
            $(".modal-body #pdflink").attr('href',kuittipath);

            $('#showfile').modal('show');
        });


        $(document).on('click', '.tapahtuma', function(e){

            e.stopPropagation();
            console.log("Show tapahtumatieto ...");
            let tid = $(this).data('tid');
            let transid = $(this).data('transid');
            let selite = $(this).data('selite');
            let amount = $(this).data('amount');
            let iban = $(this).data('iban');

            $("#tapahtuma .modal-body #tid").val(tid);
            $("#tapahtuma .modal-body #transid").val(transid);
            $("#tapahtuma .modal-body #bban").val(iban);
            $("#tapahtuma .modal-body #debtor").text(selite);
            $("#tapahtuma .modal-body #iban").text(iban);


            $('#tapahtuma').modal('show');
        });

        $('#tapahtuma').on('hidden.bs.modal', function (e) {
            e.preventDefault();
            month = $('#smon').val();
            year = $('#syear').val();
            renderTransMonth();
        })

        var tilitid = 0;

        $(document).on('click', '.tili', function(e){

            e.stopPropagation();
            console.log("Show tili ...");
            let tid = $(this).data('tid');
            tilitid = tid;

            let transid = $(this).data('transid');
            let selite = $(this).data('selite');
            let amount = $(this).data('amount');
            let sid = $(this).data('sid');


            let am = parseFloat(amount);

            if (am > 0)
            {
                $("#tili .modal-body #splitthis").text("maksu");
            }

            $("#tili .modal-body #tid").val(tid);

            if(sid)
            {
                $("#tili .modal-body #sid").val(sid);
            }

            $("#tili .modal-body #transid").val(transid);
            $("#tili .modal-body #debtor").text(selite);
            let tamount = parseFloat(amount).toFixed(2);

            $("#tili .modal-body #tamount").text(tamount);
            $("#tili .modal-body #amount").val(amount);

            $('#tili .modal-body #selction-ajax').html('Tili: ?');
            $('#tili .modal-body #tilikoodi').val("");



            $('#tili').modal('show');
        });

        $('#tili').on('hidden.bs.modal', function (e) {
            e.preventDefault();
            let classes = $(e.target).prop("classList");
            console.log("CL:"+classes);
            if (classes && classes.toString().trim() == 'modal')
            {
                tilihandle(tilitid);
                showosiot(tilitid,0);

            }
        })



        var osumma = 0;

        $(document).on('click', '.osioibtn', function(e){

            e.stopPropagation();
            let tar = $(e.target).attr('class');
            console.log("Show osio ABC ...:"+tar);
            let tid = $(this).data('tid');


            tilitid = tid;
            let transid = $(this).data('transid');
            let selite = $(this).data('selite');
            let amount = $(this).data('amount');
            let iban = $(this).data('iban');


            let am = parseFloat(amount);

            if (am > 0)
            {
                $("#osioimodal  .modal-body #stype").text("MAKSAJA:");
            }

            $("#osioimodal .modal-body #tid").val(tid);
            $("#osioimodal .modal-body #transid").val(transid);

            $("#osioimodal .modal-body #debtor").text(selite);
            $("#osioimodal .modal-body .tamount").text(amount);
            $("#osioimodal .modal-body #amount").val(amount);

            $('#osioimodal .modal-body #selction-ajax').html('Tili: ?');
            $('#osioimodal .modal-body #tilikoodi').val("");



            $('#osioimodal').modal('show');
        });

        $('#osioimodal').on('hidden.bs.modal', function (e) {
            e.preventDefault();
            let tap = "tapahtuma_"+tilitid;
            let amount = $(tap).data('amount');
            console.log("Alatili on:"+tilitid);

            showosiot(tilitid,0);

            //tilihandle(tilitid);
        })






        $('.monthrow .monthbutton').click(function(){

            console.log("Month ....");

            month = $(this).data('month');
            year = $(this).data('year');
            renderTransMonth();
            $('#smon').val(month);
            $('#syear').val(year);

            console.log("MONTH:"+month);
            console.log("YEAR:"+year);

        });



        $(document).on('click', '.subremove', function(e){

            e.stopPropagation();

            let classes = $(e.target).prop("classList");
            console.log("Remove osio ...:"+classes);

            let name = $(this).data('selite');
            let sid = $(this).data('sid');
            let tid = $(this).data('tid');
            let summa = $(this).data('summa');
            let sa = $(this).data('sa');

            let newsumma = parseFloat(summa) - parseFloat(sa);


            if (confirm("Haluatko poistaa osion: "+name))
            {

                $.ajax({

                    'url' : './removeosio.php',
                    'type' : 'POST',
                    'data' : {
                        'tid' : tid,
                        'sid' : sid
                    },
                    'success' : function(data) {
                        showosiot(tid,newsumma);
                    },
                    'error' : function(request,error)
                    {

                    }
                });
            }
            else
            {

            }
            /*
            let tid = $(this).data('tid');

            tilitid = tid;
            let transid = $(this).data('transid');
            let selite = $(this).data('selite');
            let amount = $(this).data('amount');
            let iban = $(this).data('iban');


            let am = parseFloat(amount);

            if (am > 0)
            {
                $("#osioimodal  .modal-body #stype").text("MAKSAJA:");
            }

            $("#osioimodal .modal-body #tid").val(tid);
            $("#osioimodal .modal-body #transid").val(transid);

            $("#osioimodal .modal-body #debtor").text(selite);
            $("#osioimodal .modal-body .tamount").text(amount);
            $("#osioimodal .modal-body #amount").val(amount);

            $('#osioimodal .modal-body #selction-ajax').html('Tili: ?');
            $('#osioimodal .modal-body #tilikoodi').val("");



           // $('#osioimodal').modal('show');

             */
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


    <form id="mform">
        <input type="hidden" name="smon" id="smon" value=""/>
        <input type="hidden" name="syear" id="syear" value=""/>

    </form>


    <div style="" class="monthrow">
        <?php
        /*
        $thismonth = date("m-Y");

        $oneMonthAgo = new \DateTime('1 month ago');
        $prevmonth = $oneMonthAgo->format('m-Y');

        $twoMonthAgo = new \DateTime('2 month ago');
        $prevmonth2 = $twoMonthAgo->format('m-Y');
        */



        for ($i = 0; $i < 6; $i++)
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
    <!--
    <div style="" class="kassarow">

    Tulot: <span id="tulot" class="tulosdata" style="color: green;"></span>
    Menot: <span id="menot" class="tulosdata"></span>
    Tulos: <span id="kassa" class="tulosdata"></span>

    </div>
    -->


    <div class="accordion accordion-flush" id="accordionExample" >

        <?php
        //var_dump($data);
        //
        $tulot = 0;
        $menot = 0;



        $dmaara = sizeof($data);

        for ($i = 0; $i < $dmaara; $i++)
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

            $explain = utf8_encode($explain);
            //TRANSFER
        }

        $tilit = 0;

        if (!empty($data[$i]['tilit']))
        {
            $tilit = intval($data[$i]['tilit']);
        }

        $iban = set_safe($data[$i]['bban']);

        $ali =  containsAlias($iban);

        if (!empty($ali) && strlen($ali))
        {
            $explain = $ali ."*";
        }

        $small = substr($explain,0,20);

        $amount = $data[$i]['amount'];
        $vt_amount =  $data[$i]['amount'];
        $vt = $data[$i]['vt'];



        $tid = $data[$i]['tid'];
        $transid = $data[$i]['transactionId'];

        $kuittiselite = $data[$i]['selite'];
        $kuitit = $data[$i]['kuitit'];

        $debtor = $data[$i]['debtorName'];
        $creditor = $data[$i]['creditorName'];




        $st = "defaultdot";
        if(!empty($kuitit) || intval($kuitit) > 0)
        {
            $st = "greendot";

        }

        $color = "green";



        $raha = floatval($amount);
        /*
        if ( $raha > 0)
        {
            $color = "red";

            $tulot = $tulot + $raha;

        }
        else
        {
            $menot = $menot + $raha;
        }
        */
        $amount = floatval($amount);

        //error_log(" VT_AMOUNT: ".$vt_amount."  VT: ".$vt."\n\n");

        if ( floatval($vt_amount) > 0 && intval($vt) === 1 ) 
        {
            $color = "red";
            $amount = -1 * abs($amount);

        }
        else if ( floatval($vt_amount) < 0 && intval($vt) === 1 ) 
        {
            error_log("Goes green in: ".$amount."  ....\n");
            $amount =  abs($amount);
            error_log("Goes green out: ".$amount." ....\n");
  
        }

        else if (floatval($amount) < 0)
        {
           // $amount = -1 * abs($amount);
  
        }


        $status = trim($data[$i]['status']);

        $til = "defaultrec";
        $tilimerkki = "?";

        if($tilit > 0)
        {
            $til = "greenrec";
            $tilimerkki = " ...";
        }


        $selite =  $explain." ".$amount;


        //$tdate = date_create( $data[$i]['bookingDate']);
        $tdate = date_create_from_format("ymd",$data[$i]['bookingDate']);
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

        $selitebtn =  "&nbsp;Selite";

        if (!empty($kuittiselite) && strlen($kuittiselite ) > 9)
        {
            $selitebtn =  "&nbsp;Selite";

        }





        $actionper = "";
        if (!empty($debtor) && strlen(trim($debtor)) > 2)
        {
            $actionper = trim($debtor);
        }
        elseif (!empty($creditor) && strlen(trim($creditor)) > 2)
        {
            $actionper = trim($creditor);
        }
        else
        {
            $actionper = trim($kuitti);
        }

            ?>

        <div class="accordion-item" data-tapahtuma="<?php echo($tid)?>"  data-summa="<?php echo($amount)?>" data-state="0" id="tapahtuma_<?php echo($tid)?>">
            <h2 class="accordion-header" id="heading_<?php echo($acval)?>">
                <button class="accordion-button collapsed dooper" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?php echo($acval)?>" aria-expanded="<?php echo($expan)?>" aria-controls="collapse_<?php echo($acval)?>">

                    <div class="status"><span class="dot <?php echo($st)?>" id="dot_<?php echo($tid) ?>"></span></div>


                    <div class="recs"><span class="rec <?php echo($til)?>" id="rec_<?php echo($tid) ?>"></span></div>




                    <div class="date"><?php echo($boodate)?></div> <div class="amount <?php echo($color) ?>"><?php echo($amount)?> &euro;</div>   <span class="desktop"><?php echo($explain)?></span> <span class="mobile"><?php echo($small )?></span>
                </button>
            </h2>
            <div id="collapse_<?php echo($acval)?>" class="accordion-collapse collapse <?php echo($show)?>" aria-labelledby="heading_<?php echo($acval)?>" data-bs-parent="#accordionExample">
                <div class="accordion-body">

                    <table style="width:100%;">
                        <tr>
                            <td>
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
                     <div style="width:100%; height:15px;"></div>

                                <div id="maintili_<?php echo($tid)?>">

                     <div style="width:100%;">TILINIMI: <span class="settili" id="tilinimi_<?php echo($tid)?>"><?php echo( $tilimerkki)?></span></div>
                                <div style="width:100%; height:10px;"></div>
                     <div style="width:100%;">TILIKOODI: <span  class="settili" id="tilikoodi_<?php echo($tid)?>"><?php echo( $tilimerkki)?></span></div>
                                    <div style="width:100%; height:10px;"></div>
                    <div style="width:100%;">TILIALV: <span  class="settili" id="tilialv_<?php echo($tid)?>"><?php echo( $tilimerkki)?></span></div>

                            </div>

                                <div id="subtilitholder_<?php echo($tid)?>">

                                </div>




                            </td>
                            <td style="float:right;">
                                <?php
                                if(!empty( $iban) && strlen($iban) > 3 && $aliasenabled)
                                {
                                    ?>

                                    <button type="button"  data-tid="<?php echo($tid) ?>" data-transid="<?php echo($transid) ?>" data-iban="<?php echo($iban) ?>" data-selite="<?php echo($actionper) ?>" data-amount="<?php echo($amount) ?>" class="btn btn-outline-success tapahtuma"><i class="fa fa-star" style="font-size:1.1em;"></i></button>
                                    <?php
                                }
                                ?>

                            </td>
                        </tr>
                    </table>

                    <br/><br/>
                    <div class="container" style="">
                        <div class="row" style="">
                                <div class="col" style="">

                                    <div id="kuitti_<?php echo($tid)?>">

                                    </div>
                                    <div style="clear:both;"></div><br/>
                                    <div id="selite_<?php echo($tid)?>">
                                        <?php echo($kuittiselite )?>
                                    </div>
                                    <div style="clear:both;"></div><br/>




                                    <table>
                                        <tr>
                                            <td>
                                                <button type="button"  data-tid="<?php echo($tid) ?>" data-transid="<?php echo($transid) ?>" data-selite="<?php echo($kuitti) ?>" data-amount="<?php echo($amount) ?>" class="btn btn-outline-success add-kuitti <?php echo($kbtn) ?>"><i class="fa fa-plus" style="font-size:1.1em;"></i>&nbsp;Kuitti</button>

                                            </td>
                                            <td>
                                                <button type="button"  data-tid="<?php echo($tid) ?>" data-transid="<?php echo($transid) ?>" data-selite="<?php echo($kuitti) ?>" data-amount="<?php echo($amount) ?>" class="btn btn-outline-success add-selite s<?php echo($kbtn) ?>"><i class="fa fa-plus" style="font-size:1.1em;"></i><?php echo($selitebtn); ?></button>

                                            </td>

                                            <td>
                                                <button type="button" id="maintilibtn_<?php echo($tid) ?>" data-tid="<?php echo($tid) ?>" data-transid="<?php echo($transid) ?>" data-selite="<?php echo($kuitti) ?>" data-amount="<?php echo($amount) ?>" class="btn btn-outline-success tilibtn add-tili tili">Tiliöi</button>

                                            </td>

                                            <td>
                                                <button type="button"  data-tid="<?php echo($tid) ?>" data-transid="<?php echo($transid) ?>" data-selite="<?php echo($kuitti) ?>" data-amount="<?php echo($amount) ?>" class="btn btn-outline-success osioibtn add-osioi osioi"><i class="fa fa-plus" style="font-size:1.1em;"></i> Osioi</button>

                                            </td>


                                        </tr>
                                    </table>



                                </div>
                            </div>



                        </div>


                </div>
            </div>
        </div>




    <?php
    }
        $kassa = $tulot + $menot;

        ?>
        <script>
            $(document).ready(function () {


                $("#tulot").html('<?php echo(number_format($tulot, 2)) ?>');
                $("#menot").html('<?php echo(number_format($menot, 2))  ?>');
                $("#kassa").html('<?php echo((number_format($kassa, 2)) ) ?>');

                <?php
                if ($kassa >= 0)
                {
                    ?>
                    $("#kassa").css('color','green');

                <?php
                }
                if ($tulosdata)
                {
                    ?>
                    $(".kassarow").show();
                    <?php
                }

                ?>


        });
    </script>
    <?php
?>

    </div>

</div>


