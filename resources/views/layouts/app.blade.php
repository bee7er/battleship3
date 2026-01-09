<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sink My Boats</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">

    <link href="{{env("BASE_URL", "/")}}css/site.css?v21" rel="stylesheet">
    <!-- Javascript -->
    <script type="text/javascript" src="{{env("BASE_URL", "/")}}js/smb.js?v4"></script>
</head>
<body>

@include('partials.nav')
@include('partials.header')

@yield('content')

<div class="bs-footer-spacer">&nbsp;</div>
<div class="bs-footer">
    <div class="bs-copyright">&copy; {{ (new DateTime)->format('Y') }} Brian Etheridge v2.0</div>
</div>

<div id="cookie-warning" class="bs-cookie-warning">
    <p>This website uses cookies to ensure you get the best experience on our website.
    </p>
    <button id="close-warning">Ok, got it!</button>
</div>

@yield('page-scripts')
@yield('global-scripts')

<script type="text/javascript">
    // Delete the cookie for testing purposes
//    document.cookie = "cookieWarningAccepted=; Max-Age=0; path=/;";
//    document.cookie = "user_token=; Max-Age=0; path=/;";

    // Function to set a cookie
    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    // Function to check if a cookie exists
    function checkCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length); // Trim whitespace
            if (c.indexOf(nameEQ) == 0) return true; // Cookie found
        }
        return false; // Cookie not found
    }

    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            let c = ca[i];
            //console.log(c);
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    document.addEventListener("DOMContentLoaded", function ()
    {
        const cookieWarning = document.getElementById('cookie-warning');
        const closeBtn = document.getElementById('close-warning');

        // Check if the cookie warning has been accepted
        if (!checkCookie("cookieWarningAccepted")) {
            // Show the cookie warning with a slide-in effect
            setTimeout(function() {
                cookieWarning.classList.add('bs-show');
            }, 500); // Delay before showing
        }

        // Hide the cookie warning when the button is clicked
        closeBtn.addEventListener('click', function () {
            cookieWarning.classList.remove('bs-show');
            // Optionally hide it after a short delay
            setTimeout(function() {
                cookieWarning.style.display = 'none';
            }, 500); // Wait for the transition to end
            // Set a cookie to remember that the user accepted the cookie warning
            setCookie("cookieWarningAccepted", "true", 30); // Cookie expires in 30 days
        });
    });

</script>
</body>
</html>