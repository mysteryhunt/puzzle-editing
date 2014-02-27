<?php
        require_once "config.php";
        require_once "utils.php";
        require_once "html.php";

        $wasLoggedIn = isset($_SESSION['uid']);
        if ($wasLoggedIn) { logout(); }

        // Start the HTML
        head();

        if ($wasLoggedIn) {
                echo '<h3> You have been logged out. </h3>';
        } else {
                echo '<h3> You are already logged out.</h3>';
        }

        echo '<h2><a href="login.php">Log In</a></h2>';

        // End the HTML
        foot();

//------------------------------------------------------------------------
        function logout()
        {
                session_unset();
            session_destroy();
        }
?>
