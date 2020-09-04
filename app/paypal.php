<?php
echo file_get_contents('logs/paypal.log');
echo "<hr/>";
$data = file_get_contents("php://input");
if($data != ""){
$old = file_get_contents('logs/paypal.log');
$data = json_decode($data, true);
file_put_contents('logs/paypal.log', json_encode($data)."\n". $old);
}
?>
