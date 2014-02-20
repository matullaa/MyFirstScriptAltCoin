<?php

// Initiating DB constants

include_once 'includes/psl-config-cron.php';



// Query variable

$query = "";



//Verbose ?

echo "<b>Starting Easy Pognon Spreading......</b><br><br>";



//Opening Db

$mysqli = mysqli_connect(HOST, USER, PASSWORD, DATABASE);



if ($mysqli->connect_error) {

    die('Connect Error (' . $mysqli->connect_errno . ') '

        . $mysqli->connect_error);

    echo $mysqli->connect_errno;

}

echo $mysqli->host_info . "\n";

// Petite verif ++ nbre de compte
$resultd = mysqli_query($mysqli, "SELECT `Name`, 
count(*) nbreWallet,  
(SELECT  COUNT(DISTINCT(`Name`))  FROM `miners`) as nbreAccount
FROM `miners`
GROUP BY `Name`;");
/*

 * Getting Balances from JSON

 */

$url = "http://ipac.as52143.net/4wallet.php";

$proxy = 'tcp://fr253x-prxwe01.sgt.saint-gobain.net:8080';

$context = array(

    'http' => array(

        'proxy' => $proxy,

        'request_fulluri' => True,

    ),

);

//$context = stream_context_create($context);
//$json = file_get_contents($url, false, $context);
$json = file_get_contents($url, false);

$json_data = json_decode($json);

foreach ($json_data->Coins as $_coins) {

    foreach ($_coins as $_keys => $_val) {

        $Coins[$_keys] = $_val;

    }

}



/*

 * Looping on Coins To Send User Pognon

 */



$StmtBalance = "";

$StmtBalance = $mysqli->stmt_init();

$Users = Array();





foreach ($Coins as $_key => $val) {

    $balance = $val->balance * 0.9;

    $amount = $balance / 3;

    $coin = $_key;

    $query = "";

    $query = "SELECT Coin, Name, Wallet, WalletName, Balance FROM miners WHERE Coin = ?";



    if ($balance) {

        if ($StmtBalance = $mysqli->prepare($query)) {

            $StmtBalance->bind_param('s', $_key);

            $StmtBalance->execute();

            $result = $StmtBalance->get_result();

            $StmtBalance->close();

            while ($row = $result->fetch_assoc()) {

                unset($txId);

                $name = $row['Name'];

                $address = $row['Wallet'];
//Balance == Balance ?
                $balance = $row['Balance'];

//                $timestamp = date('Y-m-d H:i:s');
$timestamp =  time();
                $balanceIn = $balance;

                $balanceOut = $balance + $amount;

                $RequestDate = date("Y-m-d"); //$time_start;

                $CommitDate = date("Y-m-d"); //$time_end;

                ob_start();

   //             passthru('/home/altcoinsd/' . $coin . '/src/./' . $coin . 'd sendtoaddress' . $address . ' ' . $amount);

                $response['deamon_info'] = ob_get_clean();

                $response['deamon_info'] = json_decode($response['deamon_info']);

                $txId = $response['deamon_info']->{'_empty_'};

                if (!$txId) {

                    $Status = 'fakeId';

                    $txId = mt_rand(100000, 900000);

                } else {

                    $Status = 'OK';

                }



                $query = "";

                $query = "INSERT INTO transactions VALUES ('$coin', '$name', '$address', '$txId', '$RequestDate', '$CommitDate', '$amount', '$balanceIn', '$balanceOut', '$timestamp', '$Status')";

                $StmtTx = $mysqli->stmt_init();

                $StmtTx = $mysqli->prepare($query) or die ("Failed to prepared the statement!");

                $StmtTx->execute();

                $StmtTx->close();

                $query = "";

                //$balanceOut = $balanceIn + $amount;

                $query = "UPDATE miners SET Balance = '$balanceOut' WHERE Coin ='$coin' AND Name ='$name'";

                $StmtUpd = $mysqli->stmt_init();

                $StmtUpd = $mysqli->prepare($query) or die ("Failed to prepared the statement!");

                $StmtUpd->execute();

                $StmtUpd->close();

            }

        }

        echo "<br>";

    }

}



$mysqli->close();



