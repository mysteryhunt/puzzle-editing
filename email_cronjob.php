<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "utils.php";

$env_dir = getenv('ENVDIR');
$env_file = getenv('ENVFILE');
if (!empty($env_dir) && !empty($env_file)) {
    Dotenv::load($env_dir, $env_file);
}

if (isset($argv[1]) && $argv[1] === "fake") {
    sendAllEmail(FALSE);
} else {
    sendAllEmail(TRUE);
}
