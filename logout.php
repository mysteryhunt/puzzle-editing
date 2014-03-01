<?php
        require_once "config.php";
        require_once "utils.php";
        require_once "html.php";

        $wasLoggedIn = isset($_SESSION['uid']);
        if ($wasLoggedIn) { logout(); }

        // Start the HTML
        head();

        if ($wasLoggedIn) {
                echo '<div class="okmsg">You have been logged out.</div>';
        } else {
                echo '<div class="errormsg">You are already logged out.</div>';
        }

        echo '<a href="login.php" class="goto">Log In</a>';

        // End the HTML
        foot();

//------------------------------------------------------------------------
        function logout()
        {
                session_unset();
            session_destroy();
        }
?>
