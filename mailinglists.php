<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("mailinglists");

	function getMoiraEntity($email) {
            if (!endsWith($email, "@mit.edu")) {
	        return "STRING:" . $email;
	    }

	    $no_mit_edu = substr($email, 0, -strlen("@mit.edu"));
	    if (isMoiraUser($no_mit_edu)) {
	    	return "USER:" . $no_mit_edu;
            }

	    if (isMoiraList($no_mit_edu)) {
	        return "LIST:" . $no_mit_edu;
            }

	    return "STRING:" . $no_mit_edu;
        }

        function isMoiraList($listname) {
	    $command = "blanche -noauth " . escapeshellarg($listname) . " -i | grep '^Description' | grep -v '^Description: User Group$'";
	    $out = exec($command, $all_output, $return_var);
	    return (bool)$out;
        }

	function isMoiraUser($username) {
	    $command = "dig +short -t txt " . $username . ".passwd.ns.athena.mit.edu";
	    $out = exec($command, $all_output, $return_var);
	    return (bool)($all_output);
        }

	function getListType($listname) {
	    $command = "blanche -noauth " . $listname . " -i";
	    $out = exec($command, $all_output, $return_var);
	    $mailman = (bool)$out;

	    if ($return_var != 0) {
	        return "unknown";
	    }

	    foreach ($all_output as $line) {
	        if (startsWith($line, $listname . " is a Mailman list")) {
		    return "mailman";
		}
	    }

	    return "moira";
	}

	function isMemberOfList($list, $list_type, $email, $moira_entity) {
	    if ($list_type == "mailman") {
		$command = "athrun consult mmblanche " . $list . " -V " . MMBLANCHE_PASSWORDS . " | grep -F -x " . escapeshellarg($email) . " 2>&1";
		$out = exec($command, $all_output, $return_var);
		return (bool)$out;
	    } else {
	        $command = "blanche -noauth -v " . $list . " | grep -F -x " . escapeshellarg($moira_entity);
	    	$out = exec($command, $all_output, $return_var);
	        return (bool)$out;
	    }
	}

	function addToMoiraList($list, $moira_entity, $krb5ccname) {
	    $command = "KRB5CCNAME=" . $krb5ccname . " blanche " . $list . " -a " . escapeshellarg($moira_entity) . " 2>&1";
	    exec($command, $all_output, $return_var);
	    print "<p>";
	    print "Adding " . $moira_entity . " to moira list " . $list . ": ";
	    if ($return_var == 0) {
	        print "Success!";
	    } else {
	        print "Failed.<br>\n";
	        foreach ($all_output as $line) {
		    print $line . "<br>\n";
		}
	    }
	    print "</p><br>\n";
	}

	function deleteFromMoiraList($list, $moira_entity, $krb5ccname) {
	    $command = "KRB5CCNAME=" . $krb5ccname . " blanche " . $list . " -d " . escapeshellarg($moira_entity) . " 2>&1";
	    exec($command, $all_output, $return_var);
	    print "<p>";
	    print "Deleting " . $moira_entity . " from moira list " . $list . ": ";
	    if ($return_var == 0) {
	        print "Success!";
	    } else {
	        print "Failed.<br>\n";
	        foreach ($all_output as $line) {
		    print $line . "<br>\n";
		}
	    }
	    print "</p><br>\n";
	}

	function addToMailmanList($list, $email) {
	    $command = "athrun consult mmblanche " . $list . " -a " . escapeshellarg($email) . " -V " . MMBLANCHE_PASSWORDS . " 2>&1";
	    exec($command, $all_output, $return_var);
	    print "<p>";
	    print "Adding " . $email . " to mailman list " . $list . ": ";
	    if (count($all_output) == 0) {
	        print "Success!";
	    } else {
	        print "Failed.<br>\n";
	        foreach ($all_output as $line) {
		    if ($line != "") {
		        print $line . "<br>\n";
		    }
		}
	    }
	    print "</p><br>\n";
	}

	function deleteFromMailmanList($list, $email) {
	    $command = "athrun consult mmblanche " . $list . " -d " . escapeshellarg($email) . " -V " . MMBLANCHE_PASSWORDS . " 2>&1";
	    exec($command, $all_output, $return_var);
	    print "<p>";
	    print "Deleting " . $email . " from mailman list " . $list . ": ";
	    if (count($all_output) == 0) {
	        print "Success!";
	    } else {
	        print "Failed.<br>\n";
	        foreach ($all_output as $line) {
		    print $line . "<br>\n";
		}
	    }
	    print "</p><br>\n";
	}

	$email = getEmail($uid);
	$moira_entity = getMoiraEntity($email);
	$lists = explode(",", MAILING_LISTS);

	$first_time = true;
	$krb5ccname = "";
	foreach ($lists as $list) {
	    if (isset($_POST[$list . "-type"])) {
	        $list_type = $_POST[$list . "-type"];
		$old_value = $_POST[$list . "-original"];
		if (isset($_POST[$list])) {
		    $new_value = $_POST[$list];
		} else {
		    $new_value = "";
		}

		if ($old_value != $new_value) {
		    if ($first_time) {
		        print "<h2>Subscription results</h2>\n";
			$first_time = false;
		    }
		    if ($list_type == "moira") {
		        if (!$krb5ccname) {
			    $krb5ccname = tempnam(TMPDIR, "krb5ccname");
			    exec("KRB5CCNAME=" . $krb5ccname . " " . GET_KEYTAB);
			}
		        if ($new_value == "true") {
		            addToMoiraList($list, $moira_entity, $krb5ccname);
			} else {
			    deleteFromMoiraList($list, $moira_entity, $krb5ccname);
			}
		    } elseif ($list_type == "mailman") {
		        if ($new_value == "true") {
			    addToMailmanList($list, $email);
			} else {
			    deleteFromMailmanList($list, $email);
			}
		    } else {
		        print "Don't know how to manage " . $list . " (" . $list_type . ")";
		    }
		}
	    }
	}

	if ($krb5ccname) {
	    unlink($krb5ccname);
	}

	print "<h2>Subscriptions for " . $moira_entity . " (" . $email . ")</h2>";

?>

	<p>This will take about 30 seconds to load while checking
	mailman subscriptions.</p>
	<br>
	<form method="post">
	<table>
<?php
	foreach ($lists as $list) {
	    $list_type = getListType($list);
	    $is_member = isMemberOfList($list, $list_type, $email, $moira_entity);
	    print '<tr><td>';
	    print '<input name="' . $list . '" type="checkbox" value="true"';
	    if ($is_member) {
	        print ' checked';
	    }
	    print ">";
	    print '<input name="' . $list . '-original" type="hidden" value="';
	    if ($is_member) {
	        print "true";
	    }
	    print '">';
	    print '<input name="' . $list . '-type" type="hidden" value="' . $list_type . '">';
	    print "</td><td>" . $list . "</td><td>(" . $list_type . ")</td></tr>\n";
	}
?>
	</table>
	<input type="submit" value="Update subscriptions">
	</form>

<?php
        // End HTML
        foot();
?>
