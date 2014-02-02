<?php

$username = "root";
$password = "boudou00";
$database = "altcoin";
$localhost = "127.0.0.1";
$file = "sources/altcoin.json";
$db_opened = false;
/*
Opening Database to get all users candidates for retribution
 */
mysql_connect($localhost, $username, $password);
@mysql_select_db($database) or die("Unable to select database");
$query = "SELECT * FROM usersdefinition";
$resultQuery = mysql_query($query);
$numRows = mysql_numrows($resultQuery);
mysql_close();
/*
Closing Database
 */

echo "<b>Database Output</b><br><br>";
echo "<b>Number of persons:$numRows</b><br><br>";

$i = 0;
while ($i < $numRows) {

    $uName = mysql_result($resultQuery, $i, "UserName");
    $uAltCoin = mysql_result($resultQuery, $i, "UserAltCoin");
    $uWalletAddress = mysql_result($resultQuery, $i, "UserWalletAddress");
    $uBalance = mysql_result($resultQuery, $i, "UserBalance");
    $walletName = mysql_result($resultQuery, $i, "WalletName");

    // ******************** Call of fake JSON if $file is filled !!! =>to be deleted for Productive systems **************
    $globalBalance = get_balance($uAltCoin, $file);
    //$balance = get_balance($uAltCoin);

    $amountSpread = $globalBalance / 3; // On est 3 non ?

    // output results of Sql Query
    echo "<b>$uName :</b>Altcoin=> $uAltCoin,Address: $uWalletAddress,UserBalance: $uBalance,Name: $walletName,Balance: $globalBalance<hr><br>";
    echo "<b>Starting transactions.....</b>Amount to spread : $amountSpread";

    if ($globalBalance > 100) {
        // Starting Transactions.....
        $resultTransaction = array();
        $resultTransaction = start_transaction($uAltCoin, $uWalletAddress, $amountSpread, $uName, $uBalance);

        echo "<b>TxId</b>,", implode($resultTransaction);

        if ($db_opened == false) {
            $db_opened = true;
            mysql_connect($localhost, $username, $password);
            @mysql_select_db($database) or die("Unable to select database");
        }

        if ($resultTransaction['TransactionId']) {
            $query = "";
            $query = send_query($resultTransaction);
            $retVal = mysql_query($query);
            if (!$retVal) {
                die('Could not enter data: ' . mysql_error());
            } else {
                // Update User Balance
                $balance_out = $resultTransaction['BalanceOut'];
                $query = "";
                $query = "UPDATE usersdefinition SET UserBalance = '$balance_out' WHERE UserAltCoin ='$uAltCoin' AND UserName ='$uName'";
                $retVal = mysql_query($query);
                if (!$retVal) {
                    die('Could not enter data: ' . mysql_error());
                }
            }
        }

    }
    $i++;
} //EndWhile User Table
if ($db_opened == true) {
    mysql_close();
}


function send_query($_input)
{
    if (is_array($_input)) {
        $query = "";
        $Id = "";
        $user = $_input['UserAltCoin'];
        $address = $_input['UserWalletAddress'];
        $TransactionId = $_input['TransactionId'];
        $RequestDate = $_input['RequestDate'];
        $CommitDate = $_input['CommitDate'];
        $TransactionAmount = $_input['TransactionAmount'];
        $BalanceIn = $_input['BalanceIn'];
        $BalanceOut = $_input['BalanceOut'];;
        $Timestamp = date('Y-m-d H:i:s');
        if ($_input['TransactionId']) {
            $Status = "Ok";
        } else {
            $Status = "Nok";
        }

//        INSERT INTO `altcoin`.`altcointransaction` (`UserAltCoin`, `UserWalletAddress`, `TransactionId`, `RequestDate`, `CommitDate`, `TransactionAmount`, `BalanceIn`, `BalanceOut`, `Status`) VALUES ('David', '1234567', '000000', '2014-12-31', '2014-12-31', '100', '100', '200', 'OK');

//        $query = "INSERT INTO altcointransaction ('UserAltCoin', 'UserWalletAddress', 'TransactionId', 'RequestDate', 'CommitDate', 'TransactionAmount', 'BalanceIn', 'BalanceOut', 'Timestamp', 'Status') VALUES ('$user', '$address', '$TransactionId', '$RequestDate', '$CommitDate', '$TransactionAmount', '$BalanceIn', '$BalanceOut', '$Timestamp', '$Status')";
        $query = "INSERT INTO altcointransaction VALUES ('$user', '$address', '$TransactionId', '$RequestDate', '$CommitDate', '$TransactionAmount', '$BalanceIn', '$BalanceOut', '$Timestamp', '$Status')";
        return $query;
    }
}


function start_transaction($_altCoin, $_address, $_amount, $_user, $_uBalance, $_debug = null)
{
    $transactionResult = array();

    $time_start = microtime(true);
    $response['transactionId'] = '';


    if (!$_address) {
        echo erreur("Target Wallet Address missing !!");
        exit;
    }
    if ($_amount == 0) {
        echo erreur("Amount to spread is 0 !!");
        exit;
    }
    if (!$_altCoin) {
        echo erreur("Altcoin type is missing !!");
        exit;
    }

    ob_start();
    passthru('/home/altcoinsd/' . $_altCoin . '/src/./' . $_altCoin . 'd sendtoaddress' . $_address . ' ' . $_amount);
    $time_end = microtime(true);

    $response['deamon_info'] = ob_get_clean();
    $response['deamon_info'] = json_decode($response['deamon_info']);

    $transactionResult['UserAltCoin'] = $_user;
    $transactionResult['UserWalletAddress'] = $_address;

    $transactionResult['TransactionId'] = $response['deamon_info']->{'_empty_'};
    if (!$transactionResult['TransactionId']) {
        $transactionResult['TransactionId'] = mt_rand(100000, 900000);
    }

    $transactionResult['RequestDate'] = date("Y-m-d"); //$time_start;
    $transactionResult['CommitDate'] = date("Y-m-d"); //$time_end;
    $transactionResult['TransactionAmount'] = $_amount;
    $transactionResult['BalanceIn'] = $_uBalance;
    $transactionResult['BalanceOut'] = $_uBalance + $_amount;

    return ($transactionResult);

//    if ($_debug) {
//       syslog(LOG_DEBUG, 'ALTCOINdS : /home/altcoinsd/' . $quoi . '/src/./' . $quoi . ' effectuee avec succes, reponse ' . $retour);
//    }

}

// Calling Daemon miner server
function get_balance_server($_altCoin, $_method, $_debug = null)
{
    $time_start = microtime(true);
    $response['deamon'] = '';
    $retour = '';

    if (!$_altCoin) {
        echo erreur("argument manquant");
        exit;
    }
    if (!$_method) {
        echo erreur("argument manquant");
        exit;
    }

    if ($_method == 'balance') {
        ob_start();
        passthru('/home/altcoinsd/' . $_altCoin . '/src/./' . $_altCoin . 'd listaccounts');
        $response['deamon_info'] = ob_get_clean();
        $response['deamon_info'] = json_decode($response['deamon_info']);
        $response['balance'] = $response['deamon_info']->{'_empty_'};
        $json['coin'] = $_altCoin;
        $json['date'] = date('Y/m/d h:i:s');
        $json['timestamp-unix'] = microtime(true);
        $json['balance'] = $response['balance'];
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $json['duree'] = $time;
        //$retour = json_encode($json);
        $retour = $response['balance'];

        return ($retour);
//        if ($_debug) {
//            syslog(LOG_DEBUG, 'ALTCOINdS : /home/altcoinsd/' . $quoi . '/src/./' . $quoi . ' effectuee avec succes, reponse ' . $retour);
//        }
    }
}

function execInBackground($cmd)
{
    if (substr(php_uname(), 0, 7) == "Windows") {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec($cmd . " > /dev/null &");
    }
}

function get_balance($_altCoin, $_file = null)
{
    $jsonBalance = "";
    $json = "";

    if ($_file) {
        $json = json_decode(file_get_contents($_file), true);
        $jsonBalance = $json["balance"];
    } else {
        $jsonBalance = get_balance_server($_altCoin, "balance");
    }
    return $jsonBalance;
}


?>