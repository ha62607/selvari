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

        function submitdone(res)
        {
            console.log(res);
            var d = res.toString();

            if (d == 'true')
            {
                //$('#loginsuc').css('display','inline-block');
                $('#loginerr').hide();
                console.log("Login success...");
                window.location.href = 'bank.php';

            }
            else
            {
                $('#loginerr').css('display','inline-block');

            }
        }


        $("#login").submit(function (event) {

            event.preventDefault();
            console.log("Login....");
            var validform = $('form[id="login"]').valid();

            if(validform)
            {

                var formData = {
                    email: $("#email").val(),
                    pass: $("#pass").val()
                };

                $.ajax({
                    type: "POST",
                    url: "loginchek.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function (data) {
                    submitdone(data);
                });

            }
            return false;
        });


        $('form[id="login"]').validate({
            rules: {
                email: {
                    email: true,
                    required: true
                },
                pass: {
                    required: true,
                    minlength: 8
                }
            },
            messages: {
                email:
                    {
                        required: "Sähköpostiosoite puuttuu",
                        email: "Tarkista sähköpostin formaatti"
                    },
                pass:
                    {
                        required: "Anna salasana",
                        minlength: "Salasanan pituus vähintään 8 merkkiä"
                    }
            }
        });

    });
</script>
<main class="container">
    <div class="bg-light p-5 rounded">


    <div id="login">
        <h1>Kirjaudu</h1>
        <p>
            Kirjaudu järjestelmään läpäti, läpäti....
        </p>

        <div id="loginerr" style="display:none;">
            <span class="errorbig">Tuntematon tunnus tai salasana.</span>
        </div>

        <form method="POST" id="login" autocomplete="on" action="#">
            <div class="form-group">
                <label for="email">Sähköposti:</label>
                <input id="email" name="email" placeholder="email@domain.fi"  class="form-control">
            </div>
            <div class="form-group">
                <label for="pass">Salasana:</label>
                <input id="pass" name="pass" type="password"  class="form-control">
            </div>
            <div class="form-group">
                <button name="send" type="submit" class="btn btn-secondary">Kirjaudu &raquo;</button>
            </div>
        </form>
        <br/><br/><br/>

        <h3>Luo uusi tunnus</h3>
        <p>
            uus tunnus läppää
        </p>
        <p>
            <a class="btn  btn-secondary" href="./newuser.php" role="button">Luo uusi tunnus &raquo;</a>
        </p>

    </div>
    </div>
</main>

<?php include('footer.php')?>
