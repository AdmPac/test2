<?php
//добавить $_REQUEST
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/bx_root.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if(!\Bitrix\Main\Loader::includeModule('nebo.auth')){
    die("Установите модуль nebo.auth");
}

\Bitrix\Main\Loader::includeModule('im');
\Bitrix\Main\Loader::includeModule('nebo.auth');

use \Nebo\Auth\SendMessage;
use \Nebo\Auth\HelperHB;


$hb = new HelperHB("AuthTH");
$rsData = $hb->getObjHB();

global $USER;
$fromID = $_REQUEST['t'];
$toID = $_REQUEST['s'];

$marker = 0;//предлолагаем, что записи нет, но все же проверим ниже:
//echo $fromID."|".$toID;

while($arRes = $rsData->Fetch()) {
    if($arRes['UF_TH']==$fromID&&$arRes['UF_S']==$toID){
        $marker = 1;
        $id = $arRes["ID"];
        break;
    }
}

if(!$marker) exit("Запись о запросе не найдена, отправьте запрос снова");

$USER->Authorize($arRes['UF_S']);

$data = [
    "UF_SESS"=>session_id(),
    "UF_DATE"=>-1//обнуляем время - не надо обращаться к сотруднику, если уже вошел сотрудник ТХП
];
//echo $id;
$hb->addData($data,$id);

//echo session_id();

echo "Вы зашли на аккаунт id={$USER->getID()}<br>";
echo "<a href='https://".$_SERVER['SERVER_NAME']."'>Перейти на главную</a>";

$buttons = [array('TITLE' => 'Закрыть доступ', 'VALUE' => 'S', 'TYPE' => 'accept', /*'URL' => 'http://test.ru/?confirm=Y'*/)];
SendMessage::send($buttons,"Техподдержка вошла в Ваш аккаунт",$fromID,$toID);


?>