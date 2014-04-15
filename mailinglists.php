<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("mailinglists");


	$email = getEmail($uid);
	$moira_entity = getMoiraEntity($email);

	$first_time = true;
	$krb5ccname = "";
	foreach ($mailing_lists as $list => $description) {
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
		        print "<p>Don't know how to manage " . $list . " (" . $list_type . ")</p><br>";
		    }
		}
	    }
	}

	if ($krb5ccname) {
	    unlink($krb5ccname);
	}

	print "<h2>Subscriptions for " . $moira_entity . " (" . $email . ")</h2>";

?>

	<script>
	function toggle(list)
	{
	    var elm = document.getElementById(list);
	    var elmbutton = document.getElementById(list.concat('-button'));
	    if(elm.style.display == '')
	    {
	        elm.style.display = 'none'
		elmbutton.innerHTML = "Show Membership List"
	    } else if(elm.style.display == 'none')
	    {
	        elm.style.display = ''
		elmbutton.innerHTML = "Hide Membership List"
	    }
	}
	</script>

	<p>This will take about 30 seconds to load while checking
	mailman subscriptions.</p>
	<br>
	<form method="post">
	<table>
<?php
	foreach ($mailing_lists as $list => $description) {
	    $list_type = getListType($list);
	    $membership = getListMembership($list, $list_type, $moira_entity);
	    $is_member = isMemberOfList($membership, $list_type, $email, $moira_entity);
	    print '<tr>';
	    print '<td>';
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

	    print "</td>\n<td>" . $list . "</td>\n<td>" . $description . "</td>\n<td>(" . $list_type . ")</td>\n";

	    print '<td><button id="' . $list . '-button" type="button" onclick="toggle(\'' . $list . '\')">Show Membership List</button></td></tr>';

	    print '<tr id="' . $list . '" style="display:none;">';

	    print '<td colspan = 5><ul>';

	    foreach ($membership as $member) {
	        print "<li>$member</li>";
	    }

	    print "</ul></td>";

	    print "</tr>";
	    print "\n";
	    flush();
	}
?>
	</table>
	<input type="submit" value="Update subscriptions">
	</form>

<?php
        // End HTML
        foot();
?>
