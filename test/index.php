<?php


echo dirname(__FILE__);
echo "<br>";
echo realpath(__FILE__);
exit();

//############################################

function convertUrlQuery($query) {
	$queryParts = explode('&', $query);

	$params = array();
	foreach ($queryParts as $param) {
		$item = explode('=', $param);
		$params[$item[0]] = $item[1];
	}

	return $params;
}




$url = 'http://username:password@hostname/path?arg=value&var=testdir#anchor';

$p = parse_url($url);
print_r($p);

echo parse_url($url, PHP_URL_PATH);
echo '<hr>';
var_dump(convertUrlQuery($p['query']));
?>