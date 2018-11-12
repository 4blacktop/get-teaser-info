<?php
// скрипт собирает инфомрацию о рекламных блоках
// визитвеба, перебирая их по порядку

// глобальные настройки
set_time_limit(0);
header("Content-Type: text/html; charset=utf-8");
$mtime = microtime(true);
echo '<pre>';

// так будет проход подряд
for ($i = 25614; $i <= 28468; $i++) {
	$url = 'http://85.17.73.67/block_view.php?bid=' . $i;
	
// а так - избранные НЕ ДОПИСАЛ ЕЩЕ
	
	
	$f = get($url);

	// если получили пустой массив - переходим к следующей итерации цикла запроса тизеров
	if(!$f) continue;
	// print_r($f);
	file_put_contents("vw/ids/html/" . $i . ".html",$f);
	
	// поместим содержимое тегов a href в массив content и запустим его проход
	preg_match_all('#<a href[^>]*?>.*?</a>#i', $f, $content);
	
	if (mb_strlen($content[0][1],"UTF-8")===strlen(iconv("UTF-8","cp1251",$content[0][1]))) {
		// echo "<br />UTF"; 
		$charset = "UTF-8";
		}
	else {
		// echo "<br />1251"; 
		$charset = "cp1251";
		}

	echo "<br />$i\t" . ((count($content[0]))/2) . "\t$charset"; 
	flush();
	$out[] = array ($i, ((count($content[0]))/2), $charset);
	}
	
// допишем файл
$fp = fopen("id-vw.csv", 'a');
foreach ($out as $line) {
	fputcsv($fp, $line);
	}
fclose($fp); 
	
echo '<br /><br />Время работы скрипта: ' . round((microtime(true) - $mtime) * 1, 4) . ' с.';	
	
function get($url) {
 	// случайный прокси
	$proxyList = array ();
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
	// curl_setopt($ch, CURLOPT_PROXY, $proxy); 
	// echo "<br />$proxy\t";
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$answer  = curl_exec($ch); 
	curl_close ($ch); 
	return $answer;
	} 

?>