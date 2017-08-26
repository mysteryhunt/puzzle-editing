<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "db-func.php";

function session_handler_open() {
    return TRUE;
}

function session_handler_close() {
    session_handler_gc(get_cfg_var("session.gc_maxlifetime"));
    return TRUE;
}

function session_handler_read($id) {
    $sql = sprintf(
        "UPDATE sessions SET timestamp=CURRENT_TIMESTAMP() WHERE id='%s'",
        mysql_real_escape_string($id));
    query_db($sql);

    $sql = sprintf(
        "SELECT data FROM sessions WHERE id='%s'",
        mysql_real_escape_string($id));
    $data = get_element_null($sql);
    if ($data == NULL) {
        return "";
    } else {
        return $data;
    }
}

function session_handler_write($id, $data) {
    $sql = sprintf(
        "INSERT INTO sessions (id, data) VALUES ('%s', '%s') ON DUPLICATE KEY UPDATE data='%s'",
        mysql_real_escape_string($id),
        mysql_real_escape_string($data),
        mysql_real_escape_string($data));
    query_db($sql);
}

function session_handler_destroy($id) {
    $sql = sprintf("DELETE FROM sessions WHERE id='%s'", mysql_real_escape_string($id));
    query_db($sql);
    return TRUE;
}

function session_handler_gc($lifetime) {
    $sql = sprintf(
        "DELETE FROM sessions WHERE timestamp < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL %d SECOND)",
        mysql_real_escape_string($lifetime));
    query_db($sql);
}

session_set_save_handler(
    'session_handler_open',
    'session_handler_close',
    'session_handler_read',
    'session_handler_write',
    'session_handler_destroy',
    'session_handler_gc'
);

session_start();
