<?php
// парсинг тизеров с визитвеба

// создать каталоги
// vw
// vw/csv
// vw/img
// vw/out

// сохраняет (дописывает) информацию в файл vw/csv/vw-ВРЕМЯ.csv
// сохраняет уникальные картинки в каталог vw/img




// глобальные настройки
set_time_limit(0);
header("Content-Type: text/html; charset=utf-8");
$mtime = microtime(true);
$tizers = array();
// echo '<pre>';

// настройки кол-ва проходов
$iMax = 10;

// настройки задержки
$pauseMin = 50;
$pauseMax = 70;

// $counterNum = 0;
/* // общий счетчик элементов
$counterAll = 0;
// счетчик уникальных (сохраненных) картинок
$counterPics = 0;
// счетчик дублей (пропущенных) картинок
$counterDouble = 0;
// счетчик урлов
$counterUrl = 0; */


// гороскопы тут 7093 7999 8002
// гороскопы утф8 45042 - ХМЛ!!!
$urls =  array(
'http://85.17.73.67/block_view.php?bid=44977',
'http://85.17.73.67/block_view.php?bid=19469',
'http://85.17.73.67/block_view.php?bid=10212', //100 игровых
);

for ($prohod = 1; $prohod <= 144; $prohod++) {
echo '<br /><br />Проход: ' . $prohod; flush();
for ($i = 1; $i <= $iMax; $i++) {
	// $url = 'http://85.17.73.67/block_view.php?bid=45559';
	// $url = 'http://85.17.73.67/block_view.php?bid=24436'; // radiorecord.fm
	// $url = 'http://85.17.73.67/block_view.php?bid=4072'; // http://babyuser.net
	// $url = 'http://85.17.73.67/block_view.php?bid=23286'; // gamebox.kz/ ЖОООООООООООООООООООООООООООООООПА ВИНДОВЗ2151
	
	// http://85.17.73.67/block_view.php?bid=2619 //UTF-8 25 Тизеров игры
	// $url = 'http://85.17.73.67/block_view.php?bid=11453'; // UTF-8 18 Тизеров игры
	// $urls = 'http://85.17.73.67/block_view.php?bid=44977'; // UTF-8 20 Тизеров игры
	// $url = 'http://85.17.73.67/block_view.php?bid=44976'; // UTF-8 20 Тизеров игры
	// $url = 'http://85.17.73.67/block_view.php?bid=9520'; // адалт 30 тизеров
	
	$url = $urls[array_rand ($urls)];
	$f = get($url);
	echo '<br />Цикл: ' . $i; flush();

	// если получили пустой массив - переходим к следующей итерации цикла запроса тизеров
	if(!$f) continue;
	
	// поместим содержимое тегов a href в массив content и запустим его проход
	preg_match_all('#<a href[^>]*?>.*?</a>#i', $f, $content);
	foreach($content[0] as $key => $line) {
	
	// определим и преобразуем кодировку к UTF-8
	if (mb_strlen($line,"UTF-8")===strlen(iconv("UTF-8","windows-1251",$line))) {
		// $charset = "UTF-8"; echo "<br />UTF"; 
		$line = $line;
		}
	else {
		// $charset = "cp1251";	// echo "<br />1251"; 
		$line = iconv("windows-1251", "UTF-8", $line);
		}
	
	// определим, есть ли в текущей ячейке картинка или текст
	$pos = stripos($line, 'img');
	if ($pos === false) {
		$tizers[($key - 1)][0] = strip_tags($line);
		// $tizers[($key - 1)][2] = $line;
		}
	else {
		preg_match('#<img src=\'(.*)\'#isU', $line, $imgUrl);
		$tizers[$key][1] = $imgUrl[1];
		}
	
	if (!file_exists('vw/img/' . array_pop(explode("/", $imgUrl[1])))) {
		$picContent = file_get_contents($imgUrl[1]);
		if ($picContent != null)
			{
			file_put_contents('vw/img/'.array_pop(explode("/", $imgUrl[1])), $picContent);
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

	}
	$pause = rand($pauseMin,$pauseMax);
	echo ' + ' . $pause; flush();
	sleep($pause);
	


	// допишем файл
	$fp = fopen("vw/csv/vw-".date("Ymd").".csv", 'a');
	foreach ($tizers as $line) {
		fputcsv($fp, $line);
		}
	fclose($fp);

	// обнулим массив
	$tizers = null;
	}	
}
// вывод статистики по работе скрипта
echo '<br /><br />Время работы скрипта: ' . round((microtime(true) - $mtime) * 1, 4) . ' с.';
echo '<br />Обработано изображений: ' . $counterAll;
echo '<br />Сохранено изображений: ' . $counterPics;
echo '<br />Ошибок скачивания изображений: ' . $counterError;
echo '<br />Пропущено дублей изображений: ' . $counterDouble;
// echo '</pre>';

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
	// curl_setopt($ch, CURLOPT_PROXY, $proxy); echo "$proxy: ";
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$answer  = curl_exec($ch); 
	curl_close ($ch); 
	return $answer;
	} 
?>