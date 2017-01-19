<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "utils.php";

if (isset($argv[1]) && $argv[1] === "fake") {
    sendAllEmail(FALSE);
} else {
    sendAllEmail(TRUE);
}
