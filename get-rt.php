<?php
// парсинг тизеров с редтрама
// сохраняет (дописывает) информацию в файл csv/mg-ВРЕМЯ.csv
// сохраняет уникальные картинки в каталог mg-pics
// для анализа существует файл mg-get-stats.php


// AddGood(219151,'СУПЕР гаджеты для автолюбителей','Всеукраинский интернет магазин МОБИЛЛАК','581.00','UAH','http://www.mobilluck.com.ua/katalog/gps/?utm_source=redtram&utm_medium=cpc&utm_campaign=gps', 1028, '4db62c4bb5d8c448a157982608c021ca');
// http://img3.ru.redtram.com/200x200/202014.jpg

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
// счетчик урлов
$counterUrl = 0;

for ($prohod = 1; $prohod <= 100; $prohod++) {
for ($i = 1; $i <= $iMax; $i++) {
	echo '<br />Проход ' . $i . ": "; flush();
	$url = 'http://g4p.redtram.com/?i=1028&p='.$i;
	$f = strip_tags(get($url));

	
	if(!$f) continue;
	preg_match_all('@\(([^\]\[]+)\)@Uis', $f, $m);
		
	// удалим первый элемент массива, потому что он мусорный, а я не умею писать нормальные регулярки
	unset ($m[1][0]);
	

	foreach($m[1] as $s) {
		$tmp = array();
		list($tmp['id'], $tmp['title'], $tmp['desc'], $tmp['price'], $tmp['curr'],$tmp['url']) = str_getcsv ($s, "," , "'");;
		$tmp['img'] = 'http://img3.ru.redtram.com/200x200/'.$tmp['id'].'.jpg';
		
		$tizers[] = $tmp;
		if ($tmp['url'] != null) {
			$counterUrl++;
			}

		if (!file_exists('rt/img/' . array_pop(explode("/", $tmp['img'])))) {
			$picContent = file_get_contents($tmp['img']);
			if ($picContent != null)
				{
				file_put_contents('rt/img/'.$tmp['id'].'.jpg', $picContent);
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
$fp = fopen("rt/csv/rt-".date("Ymd").".csv", 'a');
foreach ($tizers as $line) {
	fputcsv($fp, $line);
	}
fclose($fp);

// обнулим массив и выведем инфу
$tizers = null;
echo '<br /><br />Цикл ' . $prohod . ': ' . date("Ymd-Hi") . '<hr />';
}	

// вывод статистики по работе скрипта
echo '<br /><br />Время работы скрипта: ' . round((microtime(true) - $mtime) * 1, 4) . ' с.';
echo '<br />Обработано изображений: ' . $counterAll;
echo '<br />Сохранено изображений: ' . $counterPics;
echo '<br />Ошибок скачивания изображений: ' . $counterError;
echo '<br />Пропущено дублей изображений: ' . $counterDouble;


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
	curl_setopt($ch, CURLOPT_PROXY, $proxy); echo "$proxy: ";
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$answer  = curl_exec($ch); 
	curl_close ($ch); 
	return $answer;

	} 
	
	
?>