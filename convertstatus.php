<?php
	require_once "html.php";
	require_once "db-func.php";
	
	head();

	convertStatus();
	
	echo "Status Converted!";
	
	foot();
	
	function convertStatus()
	{
		mysql_query('START TRANSACTION');
		
		$sql = 'SELECT id, pstatus, tstatus FROM puzzle_idea';
		$puzzles = get_rows($sql);

		foreach ($puzzles as $p) {
			$pid = $p['id'];
			$oldPStatus = $p['pstatus'];
			$oldTStatus = $p['tstatus'];
			
			if ($oldPStatus == '5')			// Targeted
				updateStatus($pid, '4', '8');
			else if ($oldPStatus == '7')	// Low Priority
				updateStatus($pid, '4', '7');
			else if ($oldPStatus == '17')	// High Priority
				updateStatus($pid, '4', '5');
			else if ($oldPStatus == '4')	// Medium Priority
				updateStatus($pid, '4', '6');
			else							// Not in testing
				updateStatus($pid, $oldPStatus, '9');
		}
		
		mysql_query('COMMIT');
	}

	function updateStatus($pid, $pstatus, $tstatus)
	{
		$sql = sprintf("UPDATE puzzle_idea SET pstatus='%s', tstatus='%s' WHERE id='%s'",
				mysql_real_escape_string($pstatus), mysql_real_escape_string($tstatus), mysql_real_escape_string($pid));
		query_db($sql);
	}
?>