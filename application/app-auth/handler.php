<!--<pre>-->
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/bx_root.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';


\Bitrix\Main\Loader::includeModule('im');
\Bitrix\Main\Loader::includeModule('nebo.auth');

use \Nebo\Auth\SendMessage;
use \Nebo\Auth\HelperHB;

$hb = new HelperHB("AuthTH");
$rsData = $hb->getObjHB();

global $USER;
$toID = $_REQUEST['idS'];
$fromID = $USER->getID();

$marker = false;//маркет записи для избеэжания множественного добавления
while($arRes = $rsData->Fetch()){
    if ($arRes["UF_S"] == $toID && $arRes["UF_TH"] == $fromID) {
        $marker = true;
        if($marker){
            if(abs($arRes['UF_DATE']-mktime())/60<=15) {
//                echo "Такой запрос уже производился. Ожидайте решения сотрудника в сообщении веб-мессенджера.";
                echo 21;//код состояния
                die();
            }
            $id = $arRes['ID'];
        }
    }

}

//
if(!$marker) {
    $data = [
        'UF_TH' => $fromID,
        'UF_S' => $toID,
        'UF_SESS' => session_id(),
        'UF_DATE' => mktime()
    ];
    $hb->addData($data);
}else{//обновляем время обращения
    if($arRes["UF_DATE"]==-1){
        echo 22;
//        echo "Сотрудником уже занимается техподдержка";
        die();
    }
    while($arRes = $rsData->Fetch()) {
        if($arRes['UF_TH']==$fromID&&$arRes['UF_S']==$toID){//обновляем время(новый запрос - новый отсчет времени)
            $id = $arRes["ID"];
            break;
        }
    }
    $data = [
        'UF_DATE'=>mktime()
    ];
    $hb->addData($data,$id);
}
echo 11;
//echo "Запрос отправлен. Ожидайте подтверждения в веб-мессенджер";

$buttons = [
    array('TITLE' => 'Да', 'VALUE' => 'Y', 'TYPE' => 'accept'),
    array('TITLE' => 'Нет', 'VALUE' => 'N', 'TYPE' => 'cancel'),
];

SendMessage::send($buttons, "Сотрудник техподдержки запрашивает доступ к Вашему аккаунту", $fromID, $toID);

?>
<!--</pre>-->