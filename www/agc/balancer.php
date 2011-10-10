<?php

/*
 * Author: @Marlon Williams
 * 
 * Bounce users between servers based on server load
 * 
 */

require("dbconnect.php");


#############################################
##### GET PERFORMANCE RECORDS FOR ACTIVE SYSTEMS #####
$stmt = "SELECT server_ip FROM server_performance order by start_time desc, cpu_idle_percent limit 1;";
echo $stmt;

$rslt=mysql_query($stmt, $link);
$servers_ct = mysql_num_rows($rslt);
if ($servers_ct > 0)
    {
    $row=mysql_fetch_row($rslt);
    $less_used_server_ip = $row[0];
    }
##### END SETTINGS LOOKUP #####
###########################################

# do not send to vici01 if from a remote location - use local viciweb-01 instead
$client = explode(".", getenv("REMOTE_ADDR"));
if ($less_used_server_ip == "10.11.20.47")
    {
	$less_used_server_ip = "10.11.20.48";
    if  ( ($client[0] == "10" & $client[1] == "9") ||
            ($client[0] == "192" & $client[1] == "168")
        )
        {
        header("Location: vicidial.php?relogin=YES");
        #echo "routing locally";
        }
    }

header("Location: http://$less_used_server_ip/agc/vicidial.php?relogin=YES");

#echo "http://$less_used_server_ip/agc/vicidial.php?relogin=YES";
  
?>
