<?php
// парсинг тизеров с маркетгида
// сохраняет (дописывает) информацию в файл mg.csv
// сохраняет уникальные картинки в каталог mg-pics
// для анализа существует файл mg-get-stats.php

// глобальные настройки
set_time_limit(0);
header("Content-Type: text/html; charset=utf-8");
$mtime = microtime(true);
$tizers = array();

// настройки кол-ва проходов
$iMax = 20;

// настройки задержки
$pauseMin = 60;
$pauseMax = 80;

// общий счетчик элементов
$counterAll = 0;
// счетчик уникальных (сохраненных) картинок
$counterPics = 0;
// счетчик дублей (пропущенных) картинок
$counterDouble = 0;

for ($prohod = 1; $prohod <= 100; $prohod++) {
for ($i = 1; $i <= $iMax; $i++) {
	echo "<br />Проход " . $i . ": "; flush();
	
	// http://ab.goodsblock.dt00.net/p/j/1678/'+MGDZ+'"><'+'/scr'+'ipt>'
	// $url = 'http://ab.goodsblock.dt00.net/p/j/1678/'.$i;
	// $url = 'http://goodsblock2.dt00.net/j/1678/'.$i;
	$url = 'http://aa.goodsblock.dt00.net/p/j/980/'.$i;
	$f = strip_tags(get($url));
	
	if(!$f) continue;
	preg_match_all('@\[([^\]\[]+)\]@Uis', $f, $m);

	foreach($m[1] as $s) {
		$tmp = array();
		list($t, $tmp['id'], $tmp['type'], $tmp['title'],$tmp['desc']) = array_map(function($v){return trim($v,"'");}, explode("','", $s));
		$tmp['img'] = 'http://imgg.dt00.net/'.substr($tmp['id'],0,-3).'/'.$tmp['id'].'_vb.'.($tmp['type'] == 1 ? 'jpg' : 'gif');
		$tizers[] = $tmp;

		if (!file_exists('mg/img/' . array_pop(explode("/", $tmp['img'])))) {
			$picContent = file_get_contents($tmp['img']);
			if ($picContent != null)
				{
				file_put_contents('mg/img/'.$tmp['id'].'_vb.'.($tmp['type'] == 1 ? 'jpg' : 'gif'), $picContent);
				echo '|'; flush();
				$counterPics++;
				}
			else {
				echo '-'; flush();
				$counterError++;
				}
			}
		else {
			echo '.'; flush();
			$counterDouble++;
			}
		
		$counterAll++;
		}
	$pause = rand($pauseMin,$pauseMax);
	echo ' + ' . $pause; flush();
	sleep($pause);
	}	


// допишем файл
$fp = fopen("mg/csv/mg-".date("Ymd").".csv", 'a');
foreach ($tizers as $line) {
	fputcsv($fp, $line);
	}
fclose($fp);

// обнулим массив и выведем инфу
echo '<br /><br />Цикл ' . $prohod . ': ' . date("Ymd-Hi") . '<hr />';
$tizers = null;
}	
	
// вывод статистики по работе скрипта
echo '<br /><br />Время работы скрипта: ' . round((microtime(true) - $mtime) * 1, 4) . ' с.';
echo '<br />Обработано изображений: ' . $counterAll;
echo '<br />Сохранено изображений: ' . $counterPics;
echo '<br />Ошибок скачивания изображений: ' . $counterError;
echo '<br />Пропущено дублей изображений: ' . $counterDouble;


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