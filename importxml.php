<?php
set_time_limit(0);

ini_set('memory_limit','4096M');

define('LANG', 's1');
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_BUFFER_USED', true);

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\File;
use Bitrix\Main\Diag\Debug;
use Bitrix\Iblock\Elements;

$_SERVER['DOCUMENT_ROOT'] = __DIR__;
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

while (ob_get_level()) ob_end_flush();

Loader::includeModule('iblock');
CModule::IncludeModule('catalog');

EventManager::getInstance()->addEventHandler(
	'importxml',
	'CustomMorizoTestTaskEvent', 
	'ModifyCustomMorizoTestTaskEvent'
);

function ModifyCustomMorizoTestTaskEvent(Event $event){
	Debug::writeToFile('Количество успешно записанных в ИБ сущностей: ' . $event->getSender()['COUNT']);
}

function addProduct($id, $name, $desc, &$count){
	$name = $id.'_'.$name;
	Elements\ElementFortestTable::add([
		'ACTIVE' => 'Y',
		'NAME' => $name,
		'DETAIL_TEXT' => $desc
	]);
	$count++;
}

$filePath = Application::getDocumentRoot() . '/file.xml';
$file = new File($filePath);
$fileContent = $file->getContents();

$xml = new CDataXML();
$xml->LoadString($fileContent);

$node = $xml->GetArray();
$count = 0;
if ($node = $xml->SelectNodes('/root')){
	foreach ($node->children() as $row){
		$data = [];
		foreach ($row->children() as $col){
			$data[] = $col->textContent();
		}
		addProduct($data[0], $data[1], $data[2], $count);		
	}
}

$event = new Event('importxml', 'CustomMorizoTestTaskEvent');
$event->send(['COUNT' => $count]);
