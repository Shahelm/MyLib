<?php

//test 098f6bcd4621d373cade4e832627b4f6
//test1 5a105e8b9d40e1329780d62ea2265d8a
//test2 ad0234829205b9033196ba818f7a872b
//test3 8ad8757baa8564dc136c1e07507f4a98
//test4 e3d704f3542b44a621ebed70dc0efe13

//test_sql_1 03b0be60b82eb591d25f06a6351b8b5d
//test_sql_2 784c3102ee155d4686d50ffa5cef6298
//test_sql_3 920d240ef7a1d48d17c886b5a1e68b67
//test_sql_4 aa7582a3394e9f2ba51840e033332907
//test_sql_5 6ea4a10dbfa156af252f32372ccaa0b6

function getWeb() {
    $webs = array(
        'web1',
        'web2',
        'web3',
        'web4',
        'web5'
    );

    return $webs[rand(0, 4)];
}

function getErrorType() {
    $types = array(
        'php',
        'sql'
    );

    return $types[rand(0,1)];
}

function getMessage($web, $type) {
    $messages = array(
        'php' => array(
            'web1' => 'test',
            'web2' => 'test1',
            'web3' => 'test2',
            'web4' => 'test3',
            'web5' => 'test4',
        ),
        'sql' => array(
            'web1' => 'test_sql_1',
            'web2' => 'test_sql_2',
            'web3' => 'test_sql_3',
            'web4' => 'test_sql_4',
            'web5' => 'test_sql_5',
        ),
    );

    return $messages[$type][$web];
}

function getDateAdd () {
    return time() - rand(0, 86400);
}

$i = 4000;
$errors = array();

while ($i > 0) {
    $web = getWeb();
    $type = getErrorType();
    $message = getMessage($web, $type);

    $errors[] = array(
        'web'     => $web,
        'type'    => $type,
        'message' => $message,
        'hash'    => md5($message),
        'dateAdd' => getDateAdd(),
    );

    $i--;
}

$start = microtime(true);

$return = array();

foreach ($errors as $error) {
    if (!isset($return[$error['web']][$error['hash']])) {
        /**
         * Сдесь вычилсить count
         */
        $return[$error['web']][$error['hash']] = $error;
    } else {
        /**
         * Также необходимо обновлять id
         */
        if ($error['dateAdd'] > $return[$error['web']][$error['hash']]['dateAdd']) {
            $return[$error['web']][$error['hash']]['dateAdd'] = $error['dateAdd'];
        }
    }
}

$end = microtime(true);

echo ($end - $start), ' C';

print_r('<pre>');print_r($return);print_r('</pre>');