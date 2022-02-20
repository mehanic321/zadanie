<?php
ini_set('memory_limit','4096M');

$md5 = md5(rand(1, 10000));
$i = 0;
while($i < 2000){
	$md5 .= md5(rand(1, 10000));
	$i++;
}
$superBigText = $md5;

$xml = '<?xml version="1.0" encoding="UTF-8" ?>
<root>';
$first_name = md5(rand(1, 10000));
$last_name = $superBigText;
$i = 0;
while($i < 4000){
	$xml .= "\r\n".'<row><id>'.$i.'</id><name>'.$first_name.'</name><desc>'.$last_name.'</desc></row>';
	$i++;
}
$xml .= "\r\n".'</root>';
file_put_contents('testxml.xml', $xml, LOCK_EX);
?>