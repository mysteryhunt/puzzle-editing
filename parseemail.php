<?php
        require_once "db-func.php";

        $file = '/Users/admin/puzzle-2011.csv';
        $fh = fopen($file, 'r');

        while (!feof($fh)) {
                $line = fgets($fh);
                $comma = explode(',', $line);
                $at = explode('@', $line);

                $user['email'] = $comma[0];
                $user['name'] = $at[0];

                $users[] = $user;
        }

        fclose($fh);

        mysql_query('START TRANSACTION');

        foreach ($users as $user) {
                $sql = sprintf("INSERT INTO user_info (username, email) VALUES ('%s', '%s')",
                                mysql_real_escape_string($user['name']), mysql_real_escape_string($user['email']));
                query_db($sql);
        }

        mysql_query('COMMIT');

?>