<?php
// скрипт парсит тизеры рекламодателя по его id

header("Content-Type: text/html; charset=utf-8");
$url = 'http://usr.marketgid.com/index/stub/partner/15202/count/999/';
$content = strip_tags(get($url));

if(!$content) continue;
preg_match_all('#\[([^\]\[]+)\]#Uis', $content, $text);
array_splice($text[1], 0, 3);
	foreach($text[1] as $s) {
		$tmp = array();
		$tmp = str_getcsv($s, ',', '"');
		$tmp['url'] = str_replace('\\', '', $tmp['0']);
		$tmp['id'] = $tmp['1'];
		$tmp['type'] = $tmp['2'];
		$tmp['title'] = str_replace('\\', '', dues($tmp['3']));
		$tmp['desc'] = str_replace('\\', '', dues($tmp['4']));
		$tmp['img'] = 'http://imgg.dt00.net/'.substr($tmp['id'],0,-3).'/'.$tmp['id'].'_vb.'.($tmp['type'] == 1 ? 'jpg' : 'gif');
		$tizers[] = $tmp;
		}
// echo "<pre>"; print_r($tizers); echo "</pre>";

foreach($tizers as $t) {
    echo "<div align='center' style='width:320px;height:350px;float:left;'><a href='{$t['url']}'><img src='{$t['img']}'></a><br />{$t['title']}<br />{$t['desc']}</div>";
	}
	
	
function dues($str)
{
    return html_entity_decode(
        preg_replace('/\\\\u([a-f0-9]{4})/i', '&#x$1;', $str),
        ENT_QUOTES, 'UTF-8'
    );
}

function get($url) { 
 	// случайный прокси
	$proxyList = file("proxyok.txt");
	$proxy = $proxyList[array_rand ($proxyList)];
	$ch = curl_init($url); 
	// curl_setopt($ch, CURLOPT_HEADER, 1); // читать заголовок
	// curl_setopt($ch, CURLOPT_TIMEOUT, 3);	
	// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);	
	// curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.61 (Windows NT 5.1; U; Edition Campaign 05; en) Presto/2.1.1");
	curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3' );
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.tmp");
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.tmp"); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	// curl_setopt($ch, CURLOPT_PROXY, $proxy); echo "$proxy: ";
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$answer  = curl_exec($ch); 
	curl_close ($ch); 
	return $answer; 
	} 

?>