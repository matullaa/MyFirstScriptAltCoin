<?php
include_once '../include/psl-config.php';

$json = file_get_contents('php://input');

if (!$json) {

    exit;
}

$obj = json_decode($json);

$con = mysql_connect(HOST, USER, PASSWORD) or die('Cannot connect to the DB');

mysql_select_db(DATABASE, $con) or die('Cannot select the DB');

$Wallet = $obj->{'Wallet'};

$WalletName = $obj->{'WalletName'};

$Miner = $obj->{'Name'};

$Coin = $obj->{'Coin'};

$query = "UPDATE miners SET Wallet='{$Wallet}', WalletName='{$WalletName}' WHERE Name='$Miner' AND Coin='$Coin'";

$result = mysql_query($query, $con) or die('Errant query:  ' . $query);

mysql_close($con);

$posts = array(1);

header('Content-type: application/json');

echo json_encode(array('posts' => $posts));

?>