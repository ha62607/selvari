<!doctype html>
<html lang="fi">
<?php
$login = false;
if (!empty($_SESSION['logged'] ) && $_SESSION['logged'] === '1')
{
    $login = true;
}

if (!isset($title))
{
    $title = "Tilikirjantipito - Selvari";
}



?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="manifest" href="./manifest.json">
    <script defer src="./site.js"></script>
    <meta name="description" content="">
    <meta name="author" content="Hannu Ala-Harja - Primus72 Oy">
    <meta name="generator" content="Tamkonto 0.101.0">
    <title><?php echo($title)?></title>

    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic.min.css" integrity="sha512-LeCmts7kEi09nKc+DwGJqDV+dNQi/W8/qb0oUSsBLzTYiBwxj0KBlAow2//jV7jwEHwSCPShRN2+IWwWcn1x7Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link  rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">



    <script>

    </script>

    <script>
        function parseJwt (token) {
            console.log("Aaaa....");

            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            console.log(jsonPayload);
            return JSON.parse(jsonPayload);
        };

    </script>

    <link href="./style.css" rel="stylesheet">

<!--
    <link href="./dist/css/bootstrap.min.css" rel="stylesheet">


    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script src="./main.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>


    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    -->
    <!-- Custom styles for this template -->

    <script  src="./libs/js/jquery-3.6.0.min.js"></script>
    <script  src="./libs/js/jquery.validate.min.js"></script>
    <script   src="./main.js"></script>
    <link   href="./libs/css/bootstrap.min.css" rel="stylesheet">
    <script   src="./libs/js/bootstrap.bundle.min.js"></script>
    <link  href="./libs/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script   src="./libs/js/bootstrap-toggle.min.js"></script>
    <link  rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script   src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <link  href="navbar-top.css" rel="stylesheet">



</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark navbar-custom bg-success mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="./index.php">Selvari</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="" href="./index.php">Etusivu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./bank.php">Pankkitapahtumat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./files.php">Tiedostot</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="./settings.php">Asetukset</a>
                </li>
                <?php
                if ($login)
                {
                ?>

                <?php
                }
                ?>
                <!--
                <li class="nav-item">
                    <a class="nav-link disabled">Disabled</a>
                </li>
                -->
            </ul>
            <form class="d-flex desktop" role="search">


                <?php
                if (!$login || (isset($destroy) && $destroy))
                {
                ?>
                <a href="./login.php" class="btn btn-light loginbutton_desktop" role="button" aria-pressed="true">Kirjaudu</a>
                <?php
                }
                else
                {
                    ?>

                        <table>
                            <tr>
                                <td style="color:white;"><?php echo($_SESSION['company']) ?></td>
                                <td><a class="nav-link" href="./index.php?logout=true" style="display:inline-block; margin-left:15px;"><i class="fa fa-sign-out" style="font-size:1.5em;"></i></a></td>

                            </tr>
                        </table>



                <?php
                }
                ?>

                <!--
                <div id="buttonDiv">
                </div>
                -->
            </form>

        </div>
    </div>
</nav>
<?php

if(isset($destroy) && $destroy)
{
    ?>
    <div class="alert alert-warning" role="alert" style="width: 80%; max-width: 350px; margin-left: auto; margin-right: auto; text-align:center;">
        Istuntosi on päättynyt.
    </div>
        <?php
    $login = false;
}

if (!$login && !$nologin)
{

?>
<div class="center mobile" style="width:100%; border:0px solid red; margin-left: auto; margin-right: auto; margin-bottom: 15px;">
<a href="./login.php"  class="btn  btn-outline-success btn-lg btn-block loginbutton_mobile mobile" style="display:inline-block; width:80%; margin-left: auto; margin-right: auto;">Kirjaudu</a>
</div>
<?php
}
?>

