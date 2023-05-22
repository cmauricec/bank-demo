<?php

define("IN_CODE", 1);
include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");


$customer_id = $_GET['customer_id'];


echo '<a href="login.html">User logout</a><br>';

$sql_name= "select name from CPS3740.Customers c where id = $customer_id";
$rs_name = mysqli_query($con, $sql_name); 
if ($rs_name) {
	if (mysqli_num_rows($rs_name) > 0) {
		$row = mysqli_fetch_array($rs_name);
		echo "Transactions for customer <b>" . $row['name'] . "</b><br>";
	}

}


echo '<span>You can only update <b>Note</b> column</span>';

$sql = "select * from CPS3740_2023S.Money_mauricec m natural join CPS3740.Sources s where m.sid = s.id and m.cid = $customer_id";

$results = mysqli_query($con, $sql);

if(mysqli_num_rows($results) == 0){
	die("<br>No records to display at this time");

}

echo "<form action='update_transaction.php' method='GET'>";
echo "<table margin='auto' border='1px solid black'>";
echo "<tr><th>ID</th><th>Code</th><th>Type</th><th>Amount</th><th>Source</th><th>Date</th><th>Note</th><th>Delete</th></tr>";

$i = 0;
$balance = 0;


while ($row = mysqli_fetch_array($results)) {

    $type = $row["type"]; 
    $amount = $row['amount'];

    if ($type == 'D') {
        $amount = "<span style='color: blue;'>$amount</span>";
        $type = "<span>Deposit</span>";
    } elseif ($type == 'W') {
        $amount = "<span style='color: red;'>$amount</span>";
        $type = "<span>Withdraw</span>";
    }

    echo "<input type='hidden' name='mid[$i]' value='{$row['mid']}'>";

    echo "<tr>";
    echo "<td>{$row['mid']}</td>";
    echo "<td>{$row['code']}</td>";
    echo '<td>' . $type . '</td>';
    echo '<td>' . $amount . '</td>';


    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['mydatetime']}</td>";
    echo "<td><input type='text' value='{$row['note']}' name='cnote[$i]' style='background-color:yellow;'></td>";
    echo "<td><input type='checkbox' name='cdelete[$i]' value='Y'></td>";
    echo "</tr>";

    $i++;
}

echo "</table>";
echo "<input type='hidden' name='i' value='$i'><input type='hidden' name='cid' value= $customer_id>";

//Getting the balance for the current user 

        $sql_sum= "select sum(amount) as pos_balance from CPS3740_2023S.Money_mauricec m where type = 'D' and m.cid = $customer_id";

        $sql_negative= "select sum(amount) as negative_sum from CPS3740_2023S.Money_mauricec m where type = 'W' and m.cid = $customer_id";

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
                    echo "Total balance: <span style='color: blue;'>" . $balance . "</span><br>";
                }elseif($balance < 0){
                    echo "Total balance: <span style='color: red;'>" . $balance . "</span><br>";
                }
            }
        }else {
            echo "Something is wrong with SQL:" . mysqli_error($con);
        }


echo "<input type='submit' value='Update Transaction'>";
echo "</form>";

mysqli_close($con);

?>





