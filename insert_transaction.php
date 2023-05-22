<?php

define("IN_CODE", 1);
include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$customer_id = $_GET['customer_id'];



//Error message if code exist in the table
if(!isset($_GET['code'])){
      //using die instead of echo to kill the code when an error occurs
      die('Enter transaction code');
      exit();
}elseif($_GET['code'] == ''){
      die('Enter transaction code');
}else{
      $code = mysqli_real_escape_string($con, $_GET['code']);
      $sql_match = "select code from CPS3740_2023S.Money_mauricec m where m.cid = $customer_id and code = '$code'";

      $result_note = mysqli_query($con, $sql_match);
      if($result_note){
            if(mysqli_num_rows($result_note) > 0){
                  $row = mysqli_fetch_array($result_note); 
                  if($row['code'] == $code){
                        die('Code already exist');
                    
                  }else{
                        $code = $_GET['code']; 
                  }
            }
      }
}


//error message given if the value for type is not selected 
if (!isset($_GET['type'])) {
      die('Select deposit type');
   // $error+=1; 
}elseif($_GET['type'] == ''){
      die('Select deposit type');
}else{
      $type = $_GET['type'];
}




//error message if the amount is <= 0 as well as empty 
if(isset($_GET['amount']) && $_GET['amount'] > 0){
      $amount = $_GET['amount']; 
} else {
      die('Error Amount is not acceptable'); 
}


//Error message for FK on source_id
if(isset($_GET['source_id'])){
      $source_id = $_GET['source_id'];
      $sql= "select id from CPS3740.Sources where id = $source_id";
      $result = mysqli_query($con, $sql);
      if($result){
            if(mysqli_num_rows($result) > 0){
                  while($row = mysqli_fetch_array($result)){
                        if($row['id'] != $source_id){
                              die('Select a correct FK for source id');
                        }
                  } 
            }else{
                   die('Select a correct FK for source id');

            }
      }
}else{
      die('Select a correct FK for source id');      
}


//select the latest balance and check if the withdraw amount is less then the current balance
$sql_money = "select sum(amount) as curr_balance from CPS3740_2023S.Money_mauricec m where m.cid = $customer_id and type = 'D'"; 

$result_money = mysqli_query($con, $sql_money);
if($result_money){
      if(mysqli_num_rows($result_money) > 0){
            $row = mysqli_fetch_array($result_money); 
            if($type == 'W'){
                  if($row['curr_balance'] <= $amount){
                        die('Withdraw denied, balance is too low');
                       
                  }
            }
      }
}





if(isset($_GET['cnote'])){
      $cnote = mysqli_real_escape_string($con, $_GET['cnote']); 
} else {
      die("Error note not entered "); 
}



$sql_insert = "insert into Money_mauricec (code, cid, type, amount, note,sid) values ('$code', $customer_id, '$type',$amount,'$cnote',$source_id)";

$insert_result = mysqli_query($con, $sql_insert);
if($insert_result){
      if(mysqli_affected_rows($con) > 0){
            echo "Inserted Succesfully"; 
      }else{
            echo "Insert Unsuccesfull"; 
      }

}


mysqli_free_result($result);
mysqli_free_result($result_money);
mysqli_free_result($result_note); 
mysqli_close($con);


?>