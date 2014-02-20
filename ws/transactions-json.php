<?php
include_once '../include/psl-config.php';

if (isset($_GET['user'])) {
    if (isset($_GET['format'])) {
        $format = strtolower($_GET['format']) == 'jsonp' ? 'jsonp' : 'json'; //xml is the default
    }
    if (!isset($_GET['format'])) {
        $format = 'json'; //json is the default
    }
    if (!$format) {
        $format = 'json';
    }
    if ($format == 'jsonp') {
        $callBack = isset($_GET['jsoncallback']) ? 'jsoncallback' : 'callback';
        if (is_valid_callback($callBack)) {
            $callBackFunction = $_GET[$callBack];
        } else {
            header('status: 400 Bad Request', true, 400);
            exit;
        }
    }
//    echo $format;
    $user_id = strval($_GET['user']); //no default
    $coin = strval($_GET['coin']);

    /* connect to the db */
    $link = mysql_connect(HOST, USER, PASSWORD) or die('Cannot connect to the DB');
    mysql_select_db(DATABASE, $link) or die('Cannot select the DB');
    $query = sprintf("SELECT * FROM transactions WHERE Name='%s' AND Coin='%s'", mysql_real_escape_string($user_id), mysql_real_escape_string($coin));
//    echo $query;
    $result = mysql_query($query, $link) or die('Errant query:  ' . $query);

    /* create one master array of the records */
    $transactions = Array();

    if (mysql_num_rows($result)) {
        while ($transac = mysql_fetch_assoc($result)) {
            $transactions[] = $transac;
        }
    }

    /* disconnect from the db */
    @mysql_close($link);

    if ($format == 'json') {
        header('Content-type: application/json');
        echo json_encode(array('transactions' => $transactions));
    } elseif ($format == 'jsonp' && $callBackFunction) {
        header('Content-type: application/javascript; charset=utf-8');
//        echo $callBackFunction . '(' . json_encode(array('miners' => $posts)) . ')';
        echo $callBackFunction . '(' . json_encode(array('transactions' => $transactions)) . ')';
    } else {
        header('Content-type: text/xml');
        echo '';
        foreach ($transactions as $index => $transac) {
            if (is_array($transac)) {
                foreach ($transac as $key => $value) {
                    echo '<', $key, '>';
                    if (is_array($value)) {
                        foreach ($value as $tag => $val) {
                            echo '<', $tag, '>', htmlentities($val), '</', $tag, '>';
                        }
                    }
                    echo '</', $key, '>';
                }
            }
        }
        echo '';
    }

}

function is_valid_callback($subject)
{
    $identifier_syntax
        = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
        'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
        'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
        'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
        'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
        'private', 'public', 'yield', 'interface', 'package', 'protected',
        'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $subject)
    && !in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
}