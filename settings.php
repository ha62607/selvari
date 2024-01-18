<?php
session_start();

$nologin = true;
require_once ('config.php');
require_once ('header.php')
?>

<style>
    .loginbutton_desktop, .loginbutton_mobile
    {
        display:none;
    }
</style>
<script>
    $(document).ready(function () {

        $('#toggle-event').change(function (event) {
            event.preventDefault();
            let tila = $(this).prop('checked');
            console.log(tila);

            var formData = {
                tila: tila
            };

            $.ajax({
                type: "POST",
                url: "setalias.php",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function (data) {
                console.log("Alias setted");
            });

        });
    });
</script>
<style>
    .slow .toggle-group { transition: left 0.7s; -webkit-transition: left 0.7s; }

</style>

<main class="container">
    <div class="bg-light p-5 rounded">


        <div id="login">
            <h1>Asetukset</h1>
            <p>
                Kirjaudu järjestelmään läpäti, läpäti....
            </p>

            <form method="POST" id="login" autocomplete="on" action="#">

                <div class="form-group">
                    <label for="email">Sähköposti:</label>
                    <input id="email" name="email" placeholder="email@domain.fi"  class="form-control" disabled>
                </div>

                <div class="form-group">

                    <?php
                    $tu = $_SESSION['turva'];
                    $turva = '';
                    if ($tu === 'true')
                    {
                        $axer = 'checked';
                    }

                    ?>
                    <label for="turva">Tietoturva:</label><br/>
                    <input type="checkbox" <?php echo($turva)?> data-toggle="toggle" data-on="Päällä" data-off="Poissa" data-onstyle="success" data-offstyle="warning" data-style="slow" id="turva-event" >

                </div>

                <div class="form-group">

                    <?php
                    $al = $_SESSION['alias'];
                    $axer = '';
                    if ($al === 'true')
                    {
                        $axer = 'checked';
                    }

                    ?>

                    <label for="pass">Alias-toiminto:</label><br/>
                    <input type="checkbox" <?php echo($axer)?> data-toggle="toggle" data-on="Päällä" data-off="Poissa" data-onstyle="success" data-offstyle="warning" data-style="slow" id="toggle-event" >
                    <br/>
                </div>


            </form>


        </div>
    </div>
</main>

<?php include('footer.php')?>
