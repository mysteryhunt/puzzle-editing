<?php // vim:set ts=4 sw=4 sts=4 et:
require __DIR__ . "/vendor/autoload.php";

$env_dir = getenv('ENVDIR');
$env_file = getenv('ENVFILE');
if (!empty($env_dir) && !empty($env_file)) {
    Dotenv::load($env_dir, $env_file);
} else {
    Dotenv::load(__DIR__);
}

# Require that various configuration settings be provided in the application
# environment.
Dotenv::required(array(
    'PTRON_TMPDIR',
    'PTRON_SESSION_CACHE_DIR',
    'PTRON_PICPATH',
    'PTRON_HTMLPURIFIER_CACHE_PATH',
    'PTRON_DB_NAME',
    'PTRON_DB_USER',
    'PTRON_DB_PASSWORD',
    'PTRON_DB_SERVER',
    'PTRON_AWS_ACCESS_KEY_ID',
    'PTRON_AWS_SECRET_ACCESS_KEY',
    'PTRON_AWS_BUCKET',
    'PTRON_AWS_ENDPOINT',
    'PTRON_DEVMODE',
    'PTRON_PRACMODE',
    'PTRON_FROM_EMAIL',
    'PTRON_URL',
    'PTRON_POSTPROD_URLPREFIX',
    'PTRON_PHPMYADMIN_URL',
    'PTRON_HELP_EMAIL',
    'PTRON_WIKI_URL',
    'PTRON_BUGTRACK_URL',
    'PTRON_MAILGUN_API_URL',
    'PTRON_MAILGUN_API_KEY',
    'PTRON_TRUST_REMOTE_USER',
    'PTRON_ALLOW_TESTSOLVE_PICK',
    'PTRON_ALLOW_EDITOR_PICK',
    'PTRON_MIN_EDITORS',
    'PTRON_MIN_APPROVERS',
    'PTRON_USING_TESTSOLVE_REQUESTS',
    'PTRON_USING_TESTSOLVE_TEAMS',
    'PTRON_USING_ROUND_CAPTAINS',
    'PTRON_USING_APPROVERS',
    'PTRON_USING_CREDITS',
    'PTRON_USING_AWS',
    'PTRON_ANON_TESTERS',
    'PTRON_USING_CODENAMES',
    'PTRON_MAILING_LISTS',
    'PTRON_GET_KEYTAB',
    'PTRON_MMBLANCHE_CMD',
    'PTRON_MMBLANCHE_PASSWORDS',
    'PTRON_EDITOR_MAILING_LIST',
    'PTRON_HUNT_YEAR',
    'PTRON_HUNT_DOM',
));

# The code below wraps the config environment variables in PHP
# constants. It should probably all be changed into functions at some
# point, in order to perform tasks for each config setting like "can we
# provide a correct default?" or "should we take special action when
# this setting makes no sense?"

function _parseBooleanEnv($s) {
    return strtoupper(getenv($s)) == 'TRUE';
}

define("TMPDIR", getenv('PTRON_TMPDIR'));

ini_set('default_charset', 'UTF-8');
ini_set('session.gc_maxlifetime','86400');
ini_set('session.cookie_lifetime','86400');
ini_set('session.save_path', getenv('PTRON_SESSION_CACHE_DIR'));

session_start();

//stuff you probably don't need to change
define("SELF", "$_SERVER[PHP_SELF]");
define("PICPATH", getenv('PTRON_PICPATH'));

define("DB_NAME", getenv('PTRON_DB_NAME'));
define('DB_USER', getenv('PTRON_DB_USER'));
define('DB_PASS', getenv('PTRON_DB_PASSWORD'));
define('DB_SERVER', getenv('PTRON_DB_SERVER'));

define('AWS_ACCESS_KEY', getenv('PTRON_AWS_ACCESS_KEY_ID'));
define('AWS_SECRET_KEY', getenv('PTRON_AWS_SECRET_ACCESS_KEY'));
define('AWS_BUCKET', getenv('PTRON_AWS_BUCKET'));
define('AWS_ENDPOINT', getenv('PTRON_AWS_ENDPOINT'));

define("HTMLPURIFIER_CACHE_PATH", getenv('PTRON_HTMLPURIFIER_CACHE_PATH'));

define("PTRON_FROM_EMAIL", getenv('PTRON_FROM_EMAIL'));
define("URL", getenv('PTRON_URL'));
define("POSTPROD_URLPREFIX", getenv('PTRON_POSTPROD_URLPREFIX'));
define("POSTPROD_BETA_URLPREFIX", getenv('PTRON_POSTPROD_BETA_URLPREFIX'));
define("PHPMYADMIN_URL", getenv('PTRON_PHPMYADMIN_URL'));
define("HELP_EMAIL", getenv('PTRON_HELP_EMAIL'));
define("WIKI_URL", getenv('PTRON_WIKI_URL'));
define("BUGTRACK_URL", getenv('PTRON_BUGTRACK_URL'));

define('MAILGUN_API_URL', getenv('PTRON_MAILGUN_API_URL'));
define('MAILGUN_API_KEY', getenv('PTRON_MAILGUN_API_KEY'));

define("TRUST_REMOTE_USER", _parseBooleanEnv('PTRON_TRUST_REMOTE_USER'));

define("MIN_EDITORS", getenv('PTRON_MIN_EDITORS'));
define("MIN_APPROVERS", getenv('PTRON_MIN_APPROVERS'));

define("HUNT_YEAR", getenv('PTRON_HUNT_YEAR'));
define("HUNT_DOM", getenv('PTRON_HUNT_DOM'));

define("DEVMODE", _parseBooleanEnv('PTRON_DEVMODE'));
define("PRACMODE", _parseBooleanEnv('PTRON_PRACMODE'));
define("ALLOW_TESTSOLVE_PICK", _parseBooleanEnv('PTRON_ALLOW_TESTSOLVE_PICK'));
define("ALLOW_EDITOR_PICK", _parseBooleanEnv('PTRON_ALLOW_EDITOR_PICK'));

define("USING_TESTSOLVE_REQUESTS", _parseBooleanEnv('PTRON_USING_TESTSOLVE_REQUESTS'));
define("USING_TESTSOLVE_TEAMS", _parseBooleanEnv('PTRON_USING_TESTSOLVE_TEAMS'));
define("USING_ROUND_CAPTAINS", _parseBooleanEnv('PTRON_USING_ROUND_CAPTAINS'));
define("USING_APPROVERS", _parseBooleanEnv('PTRON_USING_APPROVERS'));
define("USING_CREDITS", _parseBooleanEnv('PTRON_USING_CREDITS'));
define("USING_AWS", _parseBooleanEnv('PTRON_USING_AWS'));
define("ANON_TESTERS", _parseBooleanEnv('PTRON_ANON_TESTERS'));
define("USING_CODENAMES", _parseBooleanEnv('PTRON_USING_CODENAMES'));

// MIT-specific mailing list features
define("MAILING_LISTS", _parseBooleanEnv('PTRON_MAILING_LISTS'));
define("GET_KEYTAB", getenv('PTRON_GET_KEYTAB'));
define("MMBLANCHE_CMD", getenv('PTRON_MMBLANCHE_CMD'));
define("MMBLANCHE_PASSWORDS", getenv('PTRON_MMBLANCHE_PASSWORDS'));
define("EDITOR_MAILING_LIST", getenv('PTRON_EDITOR_MAILING_LIST'));
$mailing_lists = array(
    "LIST" => "DESCRIPTION",
);

date_default_timezone_set('America/New_York');

// How chatty should our email alerts be by default.
define("DEFAULT_USER_EMAIL_LEVEL", 1);
