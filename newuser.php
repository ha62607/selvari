<?php include('header.php')?>
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
                $('#registersuc').css('display','inline-block');
                $('#register').hide();
                $('#registererr').hide();

            }
            else
            {
                $('#registererr').css('display','inline-block');
                $('#register').hide();

            }
        }

        $('#newuser').submit(function(event) {

            event.preventDefault();

            console.log("Sub 1");

           var validform =  $("#newuser").valid();

           console.log(validform);
           if(validform)
           {

                var formData = {
                    email: $("#email").val(),
                    firstname: $("#firstname").val(),
                    lastname: $("#lastname").val(),
                    puh: $("#puh").val(),
                    company: $("#company").val(),
                    vatid: $("#vatid").val(),
                    pass: $("#pass1").val(),
                    zipcode: $("#zipcode").val(),
                    ziparea: $("#ziparea").val(),
                    address: $("#address").val()
                };

                    console.log("Sub 2");

                    $.ajax({
                    type: "POST",
                    url: "newusersave.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function (data) {


                    submitdone(data);

                });

           }
            console.log("Sub 3");
           // event.preventDefault();
            return false;
    });




$('form[id="newuser"]').validate({
    rules: {
        firstname: {
            required: true,
            minlength: 2

        },
        lastname: {
            required: true,
            minlength: 2

        },
        company: {
            required: true,
            minlength: 2

        },
        vatid: {
            required: true,
            minlength: 9,
            maxlength: 9,
            remote: "checkcompany.php"


        },
        email: {
            email: true,
            required: true,
            remote: "checkemail.php"
        },
        pass1: {
            required: true,
            minlength: 8
        },
        pass2: {
            required: true,
            minlength: 8,
            equalTo: '#pass1'
        },
        puh: {
            required: true,
            minlength: 5,
            digits: true
        },
        zipcode: {
            required: true,
            minlength: 5,
            maxlength: 5,
            digits: true
        },
        ziparea: {
            required: true,
            minlength: 3,
            maxlength: 50
        },
        address: {
            required: true,
            minlength: 3,
            maxlength: 250
        }
    },
    // Specify validation error messages
    messages: {
        firstname:
        {
            required: "Anna etunimesi",
            minlength: "Etunimen pituus vähintään 2 merkkiä"
        },
        lastname:
        {
            required: "Anna sukunimesi",
            minlength: "Sukunimen pituus vähintään 2 merkkiä"
        },
        company:
        {
            required: "Anna yrityksen nimi",
            minlength: "Yrityksen nimen pituus vähintään 2 merkkiä"
        },
        vatid:
        {
            required: "Anna yrityksen Y-tunnnus",
            minlength: "Yrityksen Y-tunnus 9 merkkiä",
            maxlength: "Yrityksen Y-tunnus 9 merkkiä",
            remote: "Yritys on jo rekisteröity järjestelmään"
        },
        pass1:
        {
           required: "Anna salasana",
            minlength: "Salasanan pituus vähintään 8 merkkiä"
        },
        pass2:
        {
            required: "Anna salasana",
            minlength: "Salasanan pituus vähintään 8 merkkiä",
            equalTo: "Salasanan vahvistus ei täsmää"
        },
        puh:
            {
                required: "Anna puhelinnumero",
                minlength: "Vähintään 5 numeroa",
                digits: "Anna vain mumeroita"
            },
        email:
            {
                required: "Sähköpostiosoite puuttuu",
                email: "Tarkista sähköpostin formaatti",
                remote: "Tällä sähköpostilla on jo tunnus"
            },
        zipcode:
            {
                required: "Anna postinumero",
                minlength: "Postinumero on 5 merkkiä",
                maxlength: "Postinumero on 5 merkkiä"
            },
        ziparea:
            {
                required: "Anna postitoimipaikka",
                minlength: "Minimi on 3 merkkiä",
                maxlength: "Maksimi on 50 merkkiä"
            },
        address:
            {
                required: "Anna osoite",
                minlength: "Minimi on 3 merkkiä",
                maxlength: "Maksimi on 250 merkkiä"
            }
    }
});
});
</script>
<main class="container">
    <div class="bg-light p-5 rounded">
        <div id="registersuc" style="display:none;">
            <h2>Tunnuksen luonti onnistui</h2>
            <p>
                Jep jep....
            </p>

        </div>
        <div id="registererr"  style="display:none;">
            <h2>Virhetilanne...</h2>
            <p>
                Vatuix män... ota yhteys ylläpitoon.
            </p>

        </div>

        <div id="register">
        <h1>Uusi tunnus</h1>
        <p>
            Tee uusi tunnnus läpäti, läpäti....
        </p>

        <form autocomplete="on" id="newuser" method="POST">
            <div class="form-group">
                <label for="email">Sähköposti</label>
                <input id="email" name="email" placeholder="email@domain.fi" type="email" required="required" class="form-control" autofocus>
            </div>
            <div class="form-group">
                <label for="pass1">Salasana <span class="pass1error formerror"></span></label>
                <input id="pass1" name="pass1" type="password" required="required" class="form-control">
            </div>
            <div class="form-group">
                <label for="pass2">Salasanan vahvistus <span class="pass2error formerror"></span></label>
                <input id="pass2" name="pass2" type="password" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label for="firstname">Etunimi</label>
                <input id="firstname" name="firstname" type="text" required="required" class="form-control">
            </div>
            <div class="form-group">
                <label for="lastname">Sukunimi</label>
                <input id="lastname" name="lastname" type="text" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label for="puh">Puhelinnumero</label>
                <input id="puh" name="puh" type="text" class="form-control" required="required" pattern="^[0-9-]+$">
            </div>
            <div class="form-group">
                <label for="company">Yritys</label>
                <input id="company" name="company" type="text" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label for="vatid">Y-tunnus</label>
                <input id="vatid" name="vatid" placeholder="1234567-8" type="text" class="form-control" required="required"  maxlength="9" minlength="9" pattern="^[0-9-]+$">
            </div>
            <div class="form-group">
                <label for="address">Katuosoite</label>
                <textarea id="address" name="address" placeholder="Keskuskatu 3" type="text" class="form-control" required="required"  maxlength="250" minlength="5"></textarea>
            </div>
            <div class="form-group">
                <label for="zipcode">Postinumero</label>
                <input id="zipcode" name="zipcode" type="text" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label for="ziparea">Postitoimipaikka</label>
                <input id="ziparea" name="ziparea" type="text" class="form-control" required="required">
            </div>



            <div class="form-group">
                <button name="send" type="submit" class="btn btn-secondary">Luo tunnus</button>
            </div>
        </form>
        </div>

    </div>
</main>
