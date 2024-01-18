$(document).ready(function(){
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "./pinger.php",
    "method": "GET",
    "headers": {
        "content-type": "application/x-www-form-urlencoded"
    },
    "data": {
        "domain": "tamkonto.com"
    }
}

function pinger()
{
    $.ajax(settings).done(function (response) {

        if (!response)
        {
            window.location.replace("./index.php?logout=true");
        }
    })
}


    //setInterval( pinger,30000);

});