<?php

$date_start = $_GET["date_start"];
$date_end = $_GET["date_end"];

include "./connect.php";
$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error connecting database" . mysqli_error($link));
$output = '';

if(isset ($_POST["export_excel"]))
{
$query = "SELECT date,job_hiring from job_applicants where  date between '$date_start' and '$date_end' and job_hiring!='' and mark='' group by date,job_hiring ORDER BY date ASC" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

	
	while($row = mysqli_fetch_array($result))
	{
		
		$date1 = $row['date'];
		$job_hiring = $row['job_hiring'];
		
		$query3 = "SELECT COUNT(*) AS count2 FROM job_applicants where  date between '$date_start' and '$date_end' and job_hiring='$job_hiring' and mark='' and date='$date1'" or die("Error" . mysqli_error($link));
		$result5 = mysqli_query($link, $query3);
		$data = mysqli_fetch_assoc($result5);
					
					
		$output .= $row["date"] . "\t" . $row["job_hiring"] . "\t" . $data['count2'] . "\r";
		
	}
header('Content-Type: application/xls');
header('Content-Disposition: attachment; filename="downloaded.xls"');
echo $output;
}


?>
  
