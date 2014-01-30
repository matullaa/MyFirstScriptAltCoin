<?php

$balance = array(
    "coin" => "grandcoin",
    "date" => "2014\/01\/30 11:46:11",
    "timestamp-unix" => 1391078771.0706,
    "balance" => 26000.121,
    "duree" => 0.03937816619873);
$balance_out = json_encode($balance);
print_r($balance_out);
//echo $balance_out;
?>