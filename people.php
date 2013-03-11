<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        isLoggedIn();

        // Start HTML
        head("people");
        $people = getPeople();

        foreach ($people as $p) {
                printPerson($p);
        }
        // End HTML
        foot();
