<?php
session_start();

$destroy = false;

if (!empty($_REQUEST['logout']))
{
    session_destroy ();
    $destroy = true;
}

?>

<?php include('./header.php')?>


<main class="container">





    <div class="bg-light p-5 rounded">


<!--
      <div id="g_id_onload"
           data-client_id="965173975761-baucdblalsn1m1d4dhg6gmr71e2kj3e9.apps.googleusercontent.com"
           data-login_uri="http://localhost/tamkonto/"
           data-auto_prompt="false">
      </div>
      <div class="g_id_signin"
           data-type="standard"
           data-size="large"
           data-theme="outline"
           data-text="sign_in_with"
           data-shape="rectangular"
           data-logo_alignment="left">
      </div>
-->
<!--
      <div class="g-signin2" data-onsuccess="onSignIn"></div>
-->
<!--
      <pwa-auth googlekey="965173975761-baucdblalsn1m1d4dhg6gmr71e2kj3e9.apps.googleusercontent.com"></pwa-auth>
-->


    <h1>Selvari</h1>
    <p class="lead">Helpompaa kirjanpitoa ja kuittienhallintaa yrityksille.</p>

  </div>
    <br/><br/>
    <!--
<?php
    echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';
    ?>
    -->
</main>


<?php include('./footer.php')?>
