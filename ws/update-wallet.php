<?php
include_once '../include/psl-config.php';

$json = file_get_contents('php://input');
$obj = json_decode($json);
//echo $json;

$con = mysql_connect(HOST, USER, PASSWORD) or die('Cannot connect to the DB');
mysql_select_db(DATABASE, $con) or die('Cannot select the DB');
$Wallet = $obj->{'Wallet'};
$WalletName = $obj->{'WalletName'};
$Miner = $obj->{'Name'};
$Coin = $obj->{'Coin'};
//$query = sprintf("SELECT * FROM transactions WHERE Name='%s' AND Coin='%s'", mysql_real_escape_string($user_id), mysql_real_escape_string($coin));
//$query = sprintf("UPDATE miners SET (Wallet='%s', WalletName='%s') WHERE Name='%s' AND Coin='%s'", mysql_real_escape_string($Wallet), mysql_real_escape_string($WalletName), mysql_real_escape_string($Miner), mysql_real_escape_string($Coin));
$query = "UPDATE miners SET Wallet='{$Wallet}', WalletName='{$WalletName}' WHERE Name='$Miner' AND Coin='$Coin'";
//mysql_query("INSERT INTO miners (Wallet, WalletName) VALUES ('" . $obj->{'Wallet'} . "', '" . $obj->{'WalletName'} . "')") or die('Errant query:  ' . $query);
//echo $query;
$result = mysql_query($query, $con) or die('Errant query:  ' . $query);
mysql_close($con);
//
//$posts = array($json);
$posts = array(1);
header('Content-type: application/json');
echo json_encode(array('posts' => $posts));
?>