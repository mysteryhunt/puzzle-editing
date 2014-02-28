<?php
        require_once "utils.php";
        if (isset($argv[1]) && $argv[1] === "fake") fakeSendAllEmail();
        else realSendAllEmail();
?>
