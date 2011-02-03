<?php 
	require_once "html.php";
	require_once "db-func.php";
	
	head();

	convertJobs();
	
	echo "Jobs Converted!";
	
	foot();
	
	function convertJobs()
	{
		mysql_query('START TRANSACTION');
		
		$sql = 'SELECT uid, jobs FROM user_info';
		$users = get_rows($sql);

		foreach ($users as $user) {
			$uid = $user['uid'];
			
			echo "<h1>user $uid</h1>";
			
			if ($user['jobs'] != NULL) {
				echo '<p>' . $user['jobs'] . '</p>';
				$jobs = explode(',', $user['jobs']);
				foreach ($jobs as $job) {
					if ($job != '') {
						$sql = sprintf("INSERT INTO jobs (uid, jid) VALUES ('%s', '%s')",
								mysql_real_escape_string($uid), mysql_real_escape_string($job));
						query_db($sql);
					}
				}
			}
		}
		
		mysql_query('COMMIT');
	}
?>