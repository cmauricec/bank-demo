<html>
<?php
//session_start();

//define("IN_CODE", 1);
//include "dbconfig.php";


require_once('dbconfig.php');

$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");


if (isset($_GET['username'])) {
	$browser_username = mysqli_real_escape_string($con, $_GET['username']);
	$browser_password = mysqli_real_escape_string($con, $_GET['password']);
} else {
	echo "Please go to login.html first\n";
}

$customer_id;

$sql = "select login, password, name, FLOOR(DATEDIFF(CURDATE(), dob)/365) as age, concat(street, ', ', city, ', ', state, ', ', zipcode) as address, img, id from CPS3740_01.Customers1 where login = '$browser_username'";




$result = mysqli_query($con, $sql);

echo '<a href="login.html">User logout</a><br>';

//Display IP address
$ipaddress = $_SERVER['REMOTE_ADDR'];
echo "Your IP Address is: $ipaddress\n";

//Display OS information 
$browser_os = $_SERVER['HTTP_USER_AGENT'];
echo "<br>Your Browser and OS: $browser_os\n";

//Check to see if the user connecting to the login.php is from Kean 
if (strpos($ipaddress, '10.') === 0 || strpos($ipaddress, '131.125.') === 0) {
	echo "<br>You are from Kean domain.";
} else {
	echo "<br>You are NOT from Kean domain.";
}


if ($result) {
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result);
		$user_password = $row['password'];
		if ($browser_password = $user_password) {
			$user_name = $row['name'];
			$age = $row['age'];
			$address = $row['address'];
			$img = $row['img'];
			$customer_id = $row['id'];
			$base64Data = base64_encode($img);



			echo "<br>Welcome: <b>$user_name</b>\n";
			echo "<br>Age: $age\n";
			echo "<br>Address: $address\n";
			echo "<br><img src='data:image/jpeg;base64,$base64Data' />";
			//setcookie("Login", $user_name, time()+3600);
		} else {
			die("User $browser_username is in the system, but wrong password");
		}
	} else
		echo "<br>$browser_username not found\n";
} else {
	echo "Something is wrong with SQL:" . mysqli_error($con);
}




//Money table connection and display

$sql_money = "select * from CPS3740_2023S.Money_mauricec m natural join CPS3740.Sources s where m.sid = s.id and m.cid = $customer_id";
$rs_money = mysqli_query($con, $sql_money);
if ($rs_money) {
	if (mysqli_num_rows($rs_money) > 0) {



		echo '<div id="row_count"></div>';
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
			if ($type == 'D') {
				$amount = "<span style='color: blue;'>$amount</span>";
				$type = "<span>Deposit</span>";
			} elseif ($type == 'W') {
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



		//Using Javascript to count the number of rows in the money table for the current customer
		echo "<script>
    	var table = document.getElementById('money_table'); //Make a reference to the table 
      var rowCount = table.rows.length - 1; // Subtract 1 to exclude the header row
      var userName = '" . $user_name . "';
      document.getElementById('row_count').innerHTML = 'There are <b>' + rowCount + '</b> transactions for customer <b>' + userName + '</b>';//Set the div with the id row_count to the number of rows in the table 
    	</script>";
	} else {
		echo "<br>No records found for user: <b>$user_name</b>";
	}
} else {
	echo "Something is wrong with SQL:" . mysqli_error($con);
}


//Calculate the current balance for the current user 

$sql_sum = "select sum(amount) as pos_balance from CPS3740_2023S.Money_mauricec m where type = 'D' and m.cid = $customer_id";

$sql_negative = "select sum(amount) as negative_sum from CPS3740_2023S.Money_mauricec m where type = 'W' and m.cid = $customer_id";
/*
//using two values keeps the true balance

*/
$result_sum = mysqli_query($con, $sql_sum);

$result_neg = mysqli_query($con, $sql_negative);

if ($result_sum && $result_neg) {
	if (mysqli_num_rows($result_sum) > 0 && mysqli_num_rows($result_neg) > 0) {
		$row = mysqli_fetch_array($result_sum);
		$row2 = mysqli_fetch_array($result_neg);
		$positive_sum = $row['pos_balance'];
		$negative_sum = $row2['negative_sum'];
		$balance = $positive_sum - $negative_sum;

		if ($balance > 0) {
			echo "Total balance: <span style='color: blue;'>" . $balance . "</span>";
		} elseif ($balance < 0) {
			echo "Total balance: <span style='color: red;'>" . $balance . "</span>";
		}
	}
} else {
	echo "Something is wrong with SQL:" . mysqli_error($con);
}


//the search transaction button
echo '<form name="search" action="search_transaction.php" method="GET">
        <input type="hidden" name="customer_id" value="' . $customer_id . '">
        Keyword: <input type="text" name="keyword"> <input type="submit" name="submit" value="Search transaction">
    </form>';

//the add transaction button
echo '<form name ="add_transaction" action= "add_transaction.php" method="GET">
				<input type="hidden" name="user_name" value="' . $user_name . '">
				<input type="hidden" name="balance" value="' . $balance . '">
				<input type="hidden" name="customer_id" value="' . $customer_id . '">
				<input type="submit" name="add" value = "Add transaction">
				</form>';

//display transaction link 
echo '<a href="display_transaction.php?customer_id=' . $customer_id . '">Display and update transaction</a>';





mysqli_free_result($result);
mysqli_free_result($rs_money);
mysqli_free_result($result_sum);
mysqli_free_result($result_neg);
mysqli_close($con);
?>

</html>