<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("users-roles");

if (!hasServerAdminPermission($uid)) {
    echo "<div class='errormsg'>You do not have permissions for this page.</div>";
    foot();
    exit(1);
}

if (isset($_POST['changeroles'])) {
    $result = postNewUserRoles($_POST);
    if (! $result) {
        echo "<div class='errormsg'>Error Changing User Roles</div>";
    }
}

print themeUserRoleForm();
foot();


function postNewUserRoles($post) {
    // get existing user-role mapping
    $users_roles = getAllUsersAndNonAdminRoles();
    $user_role_map = array();
    foreach ($users_roles as $ur) {
        $user_role_map[$ur['uid']][$ur['role_id']] = true;
    }
    // Delete entries from user_role_map as we find them checked off in the
    // form; any which remain in the map afterwards need to be deleted.
    // Meanwhile, we also maintain a list of needed inserts.
    $to_insert = array();

    foreach ($post as $k => $v) {
        if (preg_match("/user_(\d+)_(\d+)/", $k, $m)) {
            if (isset($user_role_map[$m[1]][$m[2]])) {
                unset($user_role_map[$m[1]][$m[2]]);
            } else {
                $to_insert[] = sprintf("(%s, %s)",
                                       mysql_real_escape_string($m[1]),
                                       mysql_real_escape_string($m[2]));
            }
        }
    }
    $ok = true;
    foreach ($user_role_map as $uid => $rolearray) {
        foreach ($rolearray as $rid => $v) {
            if (!empty($rid)) {
                $sql = sprintf("DELETE FROM user_role WHERE uid = %s and role_id = %s",
                               mysql_real_escape_string($uid),
                               mysql_real_escape_string($rid));
                if (mysql_query($sql) === FALSE) {
                    $ok = false;
                }
            }
        }
    }
    if (count($to_insert) > 0) {
        $sql = "REPLACE INTO user_role VALUES " . implode(",", $to_insert);
        if (mysql_query($sql) === FALSE) {
            $ok = false;
        }
    }
    return $ok;
}

function themeUserRoleForm() {
    $html = '<h1>Users And Roles</h1>';

    $html .= '<form method="post" action="user-roles.php">' . "\n";
    $html .= '<input type="hidden" name="changeroles" value="true"/>' . "\n";
    $html .= '<table class="user-roles">' . "\n";

    $roles = getNonAdminRoles();
    $role_lookup = array();
    $html .= "<tr>\n<th>User</th>\n";
    foreach ($roles as $r) {
        $html .= "<th>{$r['name']}</th>\n";
        $role_lookup[$r['id']] = $r['name'];
    }
    $html .= "</tr>\n";

    $users_roles = getAllUsersAndNonAdminRoles();
    $user_role_map = array();
    $user_name_map = array();
    foreach ($users_roles as $ur) {
        $user_role_map[$ur['uid']][$ur['role_id']] = true;
        $fullname = htmlspecialchars($ur['fullname']);
        $username = htmlspecialchars($ur['username']);
        $user_name_map[$ur['uid']] = "{$fullname} ({$username})";
    }
    foreach ($user_name_map as $uid => $username) {
        $html .= "<tr><td>{$username}</td>\n";
        foreach (array_keys($role_lookup) as $rid) {
            $html .= "<td>";
            $checked = isset($user_role_map[$uid][$rid]) ? 'checked' : '';
            $html .= "<input type=\"checkbox\" {$checked} name=\"user_{$uid}_{$rid}\" value=\"1\"/>";
            $html .= "</td>";
        }
        $html .= "</tr>\n";
    }

    $html .= "<tr>\n<td colspan=3><input type=\"submit\" value=\"Save New Roles\"></td>\n</tr>\n";
    $html .= "</table>\n";
    $html .= "</form>\n";
    return $html;
}
