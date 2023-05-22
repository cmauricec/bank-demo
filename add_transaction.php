<?php
define("IN_CODE", 1);
include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");
echo '<a href="login.html">User logout</a><br>';

echo " <b>Add Transaction</b><br>"; 

$user_name = $_GET['user_name'];
$balance = $_GET['balance'];
$customer_id = $_GET['customer_id'];

echo "<b>". $user_name . " </b> current balance is <b>" . $balance ."</b>" ;


echo '<form name ="insert_transaction" action= "insert_transaction.php" method="GET">
			<input type="hidden" name="customer_id" value="' . $customer_id . '">
		 	Transaction code: <input type="text" name="code"><br>

		 	<!-- Deposit-->
		 	<input type="radio" name="type" value="D">
		 	<label for="deposit">Deposit</label>

		 	<!--Withdraw-->
		 	<input type="radio" name="type" value="W">
			<label for="deposit">Withdraw</label><br>

			Amount: <input type="text" name="amount"><br>
			<select name = "source_id">';
				echo '<option value=""></option>';
				$sql = "select * from CPS3740.Sources";
				$result = mysqli_query($con, $sql);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						while ($row = mysqli_fetch_array($result)) {
							echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
						}
					}
				}
				echo '</select><br> 
				Note: <input type="text" name="cnote"><br>
				<input type="submit" name="submit" >
				</form>';
		



mysqli_free_result($result); 
mysqli_close($con);



?>