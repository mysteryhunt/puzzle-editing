<?php
        require_once "html.php";
        require_once "config.php";
        require_once "secret.php";

        // Connect to database
        if (($db = mysql_connect(DB_SERVER, DB_USER, DB_PASS)) == FALSE) {
                echo 'Could not connect to database server ' . DB_SERVER . '.';
                foot();
                exit(1);
        }

        // Use UTF-8 character set for connection
        if (!mysql_set_charset('utf8')) {
                echo 'Could not set character set.';
                foot();
                exit(1);
        }

        // Select database
        if (mysql_select_db(DB_NAME, $db) == FALSE) {
                echo 'Could not select database ' . DB_NAME . '.';
                foot();
                exit(1);
        }

        // Query database.
        // Error if no result is found.
        function query_db($query)
        {
                $result = mysql_query($query);

                if ($result == FALSE)
                        db_error($query);

                return $result;
        }

        // Get one row from database, as an array.
        // Error if no result found or if more than 1 row present.
        function get_row($query)
        {
                $result = query_db($query);

                if ($result == FALSE)
                        db_error($query);

                if (mysql_num_rows($result) != 1)
                        db_error($query);

                $r = mysql_fetch_array($result);
                return $r;
        }

        // Get one row from database, as an array.
        // Return null if no result found.
        // Error if more than 1 row found.
        function get_row_null($query)
        {
                $result = mysql_query($query);

                if ($result == FALSE || mysql_num_rows($result) == 0)
                        return NULL;

                if (mysql_num_rows($result) != 1)
                        db_error($query);

                $r = mysql_fetch_array($result);
                return $r;
        }

        // Get multiple rows from database, as an array of arrays.
        // Error if no result found
        function get_rows($query)
        {
                $result = query_db($query);

                if ($result == FALSE)
                        db_error($query);

                $rows = NULL;
                while ($r = mysql_fetch_array($result)) {
                        $rows[] = $r;
                }

                return $rows;
        }

        // Get multiple rows from database, as an array of arrays.
        // Return NULL if no result found
        function get_rows_null($query)
        {
                $result = mysql_query($query);

                if ($result == FALSE || mysql_num_rows($result) == 0) {
                        return NULL;
                }

                $rows = NULL;
                while ($r = mysql_fetch_array($result)) {
                        $rows[] = $r;
                }

                return $rows;
        }

        // Get an associative array
        function get_assoc_array($query, $key_col, $val_col)
        {
                $arr = array();

                $result = mysql_query($query);
                if ($result != FALSE) {
                        while ($r = mysql_fetch_array($result)) {
                                $arr[$r[$key_col]] = $r[$val_col];
                        }
                }

                return $arr;
        }

        // Get a single datum from the database.
        // Error if no result found
        function get_element($query)
        {
                $r = get_row($query);
                if ($r == NULL)
                        return NULL;
                else
                        return $r[0];
        }

        // Get a single datum from the database.
        // Return null if no result found
        function get_element_null($query)
        {
                $r = get_row_null($query);
                if ($r == NULL)
                        return NULL;
                else
                        return $r[0];
        }

        // Get a column from the database, as an array.
        // Error if no result found
        function get_elements($query)
        {
                $result = query_db($query);
                while ($r = mysql_fetch_array($result)) {
                        $elements[] = $r[0];
                }

                return $elements;
        }

        // Get a column from the database, as an array.
        // Return NULL if no result found.
        function get_elements_null($query)
        {
                $result = mysql_query($query);

                if ($result == FALSE || mysql_num_rows($result) == 0) {
                        return NULL;
                }

                $rows = NULL;
                while ($r = mysql_fetch_array($result)) {
                        $rows[] = $r[0];
                }

                return $rows;
        }

        // Check if a query returns a result
        function has_result($query)
        {
                $result = mysql_query($query);

                if ($result == NULL)
                        return FALSE;
                if (mysql_num_rows($result) > 0)
                        return TRUE;

                //Something went wrong somewhere
                return FALSE;
        }

        // On database error, give error and stop.
        function db_error($query)
        {
                mysql_query('ROLLBACK');
                echo "An error has occurred while querying the database. Please try again. <br />";
                echo $query;
                foot();
                exit(1);
        }
?>
