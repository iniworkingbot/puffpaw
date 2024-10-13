<?php
error_reporting(0);
$list_query = array_filter(@explode("\n", str_replace(array("\r", " "), "", @file_get_contents(readline("[?] List Query       ")))));
$reff = readline("[?] Referral       ");
echo "[*] Total Query : ".count($list_query)."\n";
Awal:
for ($i = 0; $i < count($list_query); $i++) {
    $c = $i + 1;
    echo "\n[$c]\n";
    $check_reff = reff($reff, $list_query[$i]);
    echo "[*] Check Reff : ";
    if($check_reff == 'ok'){
        echo "success\n";
        $info = info($list_query[$i]);
        echo "[*] Level : ".$info['points']."\n";
        echo "[*] Check Guidance : ";
        if($info['guidance'] == 0){
            echo "failed\n";
            $guidance = guidance($list_query[$i]);
            echo "[*] Start Guidance : ";
            if($guidance == 1){
                echo "success\n";
            }
            else{
                 echo "failed\n";
            }
        }
        else{
            echo "complete\n";
        }
        $check = check_status($list_query[$i])['machineWorkTime'];
        if($check == 'H12'){
            $start = start_game($list_query[$i]);
            echo "[*] Start Game : ";
            if($start['endTime']){
                echo "success\n";
                $time = check_status($list_query[$i]);
                echo "[*] Wait Time : ".time_remain($time['endTime'])." second\n";
            }
            else{
                echo "failed\n";
            }
        }
        elseif($check == 'H1'){
            $check = check_status($list_query[$i])['waitingForCollectionBombs'];
            echo "[*] Check Complete Item : ";
            if($check){
                echo "success\n\n";
                for ($a = 0; $a < count($check); $a++) {
                    $c = $a + 1;
                    echo "[-] Item $c : ".collect_item($check[$a]['slot'], $list_query[$i])."\n";
                }
            }
            else{
                echo "failed\n";
                $start = start_game($list_query[$i]);
                echo "[*] Start Game : ";
                if($start['endTime']){
                    echo "success\n";
                    $time = check_status($list_query[$i]);
                    echo "[*] Wait Time : ".time_remain($time['endTime'])." second\n";
                }
                else{
                    echo "failed\n";
                    $time = check_status($list_query[$i]);
                    echo "[*] Wait Time : ".time_remain($time['endTime'])." second\n";
                }
            }
        }
        $inventory = inventory($list_query[$i]);
        echo "[*] Get Inventory : ";
        if($inventory){
            echo "success\n\n";
            for ($a = 0; $a < count($inventory); $a++) {
                echo "[-] ".$inventory[$a]['bombId']." : ".$inventory[$a]['amount']."\n";
            }
        }
        else{
            echo "failed\n";
        }
    }
    else{
        echo "failed\n";
    }
    sleep(10);
}
echo "\n[*] All Done!, Waiting 60 min\n";
sleep(3600);
goto Awal;




function info($query){
    $curl = curl("user/info", $query, "{}");
    return $curl;
}

function check_point($query){
    $curl = curl("point/myPoint", $query)['points'];
    return $curl;
}

function guidance($query){
    $curl = curl("user/guidance", $query, "{}");
    return $curl;
}

function check_status($query){
    $curl = curl("machine/stat", $query, "{}");
    return $curl;
}

function start_game($query){
    $curl = curl("machine/start", $query, "{\"machineWorkTime\":\"H1\"}");
    return $curl;
}

function collect_item($id, $query){
    $curl = curl("machine/collect", $query, "{\"slotId\":$id}")['bombId'];
    return $curl;
}

function inventory($query){
    $curl = curl("bomb/bag", $query);
    return $curl;
}

function time_remain($timestamp){
    $current = time();
    $time = ($timestamp - $current);
    return $time;
}

function curl($path, $auth, $body = false){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://pufflabs-api.puffpaw.app/api/v1/logged/'.$path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($body){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $headers = array();
    $headers[] = 'Accept: */*';
    $headers[] = 'Accept-Language: en-US,en;q=0.9';
    $headers[] = 'Authorization: tma '.$auth;
    $headers[] = 'Content-Type: application/json;charset=UTF-8';
    $headers[] = 'Origin: https://pufflabs.puffpaw.app';
    $headers[] = 'Referer: https://pufflabs.puffpaw.app/';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36 Edg/129.0.0.0';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $decode = json_decode($result, true)['data'];
    return $decode;
}

function reff($reff, $query){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://click.puffpaw.xyz/api/event');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $data = urlencode($query);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"n":"pageview","u":"https://pufflabs.puffpaw.app/?tgWebAppStartParam=7gCprN#tgWebAppData='.$data.'&tgWebAppVersion=7.10&tgWebAppPlatform=android&tgWebAppThemeParams=%7B%22bg_color%22%3A%22%23212121%22%2C%22text_color%22%3A%22%23ffffff%22%2C%22hint_color%22%3A%22%23aaaaaa%22%2C%22link_color%22%3A%22%238774e1%22%2C%22button_color%22%3A%22%238774e1%22%2C%22button_text_color%22%3A%22%23ffffff%22%2C%22secondary_bg_color%22%3A%22%230f0f0f%22%2C%22header_bg_color%22%3A%22%23212121%22%2C%22accent_text_color%22%3A%22%238774e1%22%2C%22section_bg_color%22%3A%22%23212121%22%2C%22section_header_text_color%22%3A%22%23aaaaaa%22%2C%22subtitle_text_color%22%3A%22%23aaaaaa%22%2C%22destructive_text_color%22%3A%22%23e53935%22%7D","d":"pufflabs.puffpaw.app","r":"https://pufflabs.puffpaw.app/?tgWebAppStartParam='.$reff.'"}');
    $headers = array();
    $headers[] = 'Host: click.puffpaw.xyz';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36';
    $headers[] = 'Content-Type: text/plain';
    $headers[] = 'Accept: */*';
    $headers[] = 'Origin: https://pufflabs.puffpaw.app';
    $headers[] = 'Referer: https://pufflabs.puffpaw.app/';
    $headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7,ms;q=0.6';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    return $result;
}
