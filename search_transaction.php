<?php

//session_start();

define("IN_CODE", 1);
include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");


if(isset($_GET['keyword'])){ 
	$keyword = mysqli_real_escape_string($con, $_GET['keyword']);
}elseif(empty($_GET['keyword'])){
	echo "Enter a word to search\n"; 
}else{
	echo "Enter a word to search\n"; 
}


$customer_id = $_GET['customer_id'];

//$customer_id = $_SESSION['ID'];

$sql= "select name from CPS3740.Customers c where id = $customer_id";

$result = mysqli_query($con, $sql); 

if ($result) {
	if (mysqli_num_rows($result)>0) {
		$row = mysqli_fetch_array($result);
		$user_name = $row['name'];
		//echo("Checkpoint 1"); 
	}

}else {
	echo "Something is wrong with SQL:" . mysqli_error($con);
}
	



//if the keyoword is * then select everything 
if($keyword == "*"){
	$sql_money = "select * from CPS3740_2023S.Money_mauricec m natural join CPS3740.Sources s where m.sid = s.id and m.cid = $customer_id";
	$rs_money = mysqli_query($con, $sql_money);
	if ($rs_money) {
		if (mysqli_num_rows($rs_money) > 0) {
			echo "The transactions in customer <b>" . $user_name . "</b> records matched keyword <b>"  . $keyword ."</b> are:<br> ";
			echo '<table id=money_table margin = "auto" border = "1px solid black">
			<tr>
			<td><b>ID</b></td>
    			<td><b>Code</b></td>
    			<td><b>Type</b></td>
    			<td><b>amount</b></td>
    			<td><b>Source</b></td>
    			<td><b>Date</b></td>
    			<td><b>Note</b></td>
    			</tr>';
    			while ($row = mysqli_fetch_array($rs_money)) {
    				$type = $row["type"]; 
      			$amount = $row['amount'];
      			if ( $type == 'D') {
      				$amount = "<span style='color: blue;'>$amount</span>";
      				$type = "<span>Deposit</span>";
      			}elseif ($type == 'W') {
      				$amount = "<span style='color: red;'>$amount</span>";
        				$type = "<span>Withdraw</span>";
      			}

			

      			echo '<tr>
      			<td>' . $row['mid'] . '</td>
      			<td>' . $row['code'] . '</td>
      			<td>' . $type . '</td>
      			<td>' . $amount . '</td>
      			<td>' . $row['name'] . '</td>
      			<td>' . $row['mydatetime'] . '</td>
      			<td>' . $row['note'] . '</td>
      			</tr>';
    			}
    			echo '</table>';
    		

    			//Getting the balance for the current user 

			$sql_sum= "select sum(amount) as pos_balance from CPS3740_2023S.Money_mauricec m where type = 'D' and m.cid = $customer_id and note like '%$keyword%'";

			$sql_negative= "select sum(amount) as negative_sum from CPS3740_2023S.Money_mauricec m where type = 'W' and m.cid = $customer_id and note like '%$keyword%'";

			$result_sum = mysqli_query($con, $sql_sum); 

			$result_neg = mysqli_query($con, $sql_negative); 

			if ($result_sum && $result_neg) {
				if (mysqli_num_rows($result_sum) > 0 && mysqli_num_rows($result_neg) > 0  ) {
					$row = mysqli_fetch_array($result_sum);
					$row2 = mysqli_fetch_array($result_neg);
					$positive_sum = $row['pos_balance']; 
					$negative_sum = $row2['negative_sum']; 
					$balance = $positive_sum - $negative_sum; 

					if($balance > 0){
						echo "Total balance: <span style='color: blue;'>" . $balance . "</span>";
					}elseif($balance < 0){
						echo "Total balance: <span style='color: red;'>" . $balance . "</span>";
					}
				}
			}else {
				echo "Something is wrong with SQL:" . mysqli_error($con);
			}
		}
	}

}else{
	$sql_search =  "select * from CPS3740_2023S.Money_mauricec m natural join CPS3740.Sources s where m.sid = s.id and m.cid = $customer_id and m.note like '%$keyword%'"; 


	$search_result = mysqli_query($con, $sql_search);
	if ($search_result) {
		if (mysqli_num_rows($search_result) > 0) {
			echo "The transactions in customer <b>" . $user_name . "</b> records matched keyword <b>"  . $keyword ."</b> are: ";
			echo '<table id=money_table margin = "auto" border = "1px solid black">
			<tr>
			<td><b>ID</b></td>
    			<td><b>Code</b></td>
    			<td><b>Type</b></td>
    			<td><b>amount</b></td>
    			<td><b>Source</b></td>
    			<td><b>Date</b></td>
    			<td><b>Note</b></td>
    			</tr>';
    			while ($row = mysqli_fetch_array($search_result)) {
    				$type = $row["type"]; 
      			$amount = $row['amount'];
      			if ( $type == 'D') {
      				$amount = "<span style='color: blue;'>$amount</span>";
      				$type = "<span>Deposit</span>";
      			}elseif ($type == 'W') {
      				$amount = "<span style='color: red;'>$amount</span>";
        				$type = "<span>Withdraw</span>";
      			}
      			echo '<tr>
      			<td>' . $row['mid'] . '</td>
      			<td>' . $row['code'] . '</td>
      			<td>' . $type . '</td>
      			<td>' . $amount . '</td>
      			<td>' . $row['name'] . '</td>
      			<td>' . $row['mydatetime'] . '</td>
      			<td>' . $row['note'] . '</td>
      			</tr>';
      		}
      		echo '</table>';


     			//Getting the balance for the current user 

			$sql_sum= "select sum(amount) as pos_balance from CPS3740_2023S.Money_mauricec m where type = 'D' and m.cid = $customer_id and m.note like '%$keyword%'";

			$sql_negative= "select sum(amount) as negative_sum from CPS3740_2023S.Money_mauricec m where type = 'W' and m.cid = $customer_id and m.note like '%$keyword%'";

			$result_sum = mysqli_query($con, $sql_sum); 

			$result_neg = mysqli_query($con, $sql_negative); 

			if ($result_sum && $result_neg) {
				if (mysqli_num_rows($result_sum) > 0 && mysqli_num_rows($result_neg) > 0  ) {
					$row = mysqli_fetch_array($result_sum);
					$row2 = mysqli_fetch_array($result_neg);
					$positive_sum = $row['pos_balance']; 
					$negative_sum = $row2['negative_sum']; 
					$balance = $positive_sum - $negative_sum; 

					if($balance > 0){
						echo "Total balance: <span style='color: blue;'>" . $balance . "</span>";
					}elseif($balance < 0){
						echo "Total balance: <span style='color: red;'>" . $balance . "</span>";
					}
				}
			}else {
			echo "Something is wrong with SQL:" . mysqli_error($con);
			}

     		}else{echo "No record Found!"; }
     	} 
     	else {
  		echo "Something is wrong with SQL:" . mysqli_error($con);
  	}
mysqli_free_result($search_result);

}



mysqli_free_result($result);

mysqli_close($con);


?>