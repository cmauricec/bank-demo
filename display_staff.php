<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$value = 10000; 
$sql="select * from dreamhome.Staff";

#$sql = "select * from dreamhome.Staff"; 
$result = mysqli_query($con, $sql); 


echo "<br>SQL: $sql\n";


if ($result) {
	if (mysqli_num_rows($result)>0) {
		echo "<TABLE border=1>\n";
		echo "<TR><TH>Staffno<TH>Fname<TH>Lname<TH>Position<TH>Sex<TH>DOB<TH>Salary<TH>Branchno";
	    while($row = mysqli_fetch_array($result)){
	    		$staffno = $row["staffNo"];
	        $fname = $row["fName"];
	        $lname = $row["lName"];
	        $position = $row["position"]; 
	        $sex= $row["sex"];
	        $dob = $row["DOB"]; 
	        $salary=$row["salary"];
	        $branchno=$row["branchNo"];

	        if ($sex =="F")      
	        	$color="red";
	        else
	        	$color="blue";
	        echo "<TR><TD>$staffno<TD>$fname<TD>$lname<TD>$position<TD><font color='$color'> $sex</font><TD>$dob<TD>$salary<TD>$branchno\n";
	    }
	    echo "</TABLE>\n";
	}
	else
		echo "<br>No record found\n";
}
else {
  echo "Something is wrong with SQL:" . mysqli_error($con);	
}
mysqli_free_result($result);
mysqli_close($con);
?>
