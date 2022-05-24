<?php
namespace Nebo\Auth;

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('im');

use Nebo\Auth\HelperHB;
use Nebo\Auth\SendMessage;

class EventMessages{
    static public function EventHandlers($module,$tags,$value){
        global $USER;
        $hb = new HelperHB("AuthTH");
        $rsData = $hb->getObjHB();


        $tags = explode('|', $tags);
        $toID = $tags[0];//к кому было сообщение
        $fromID = $tags[1];//от кого было сообщение

        if($value=='S') {//сотрудник запретил доступ в процессе
            while($arRes = $rsData->Fetch()){
                if($arRes["UF_S"]==$toID&&$arRes["UF_TH"]==$fromID){
                    $session_id_to_destroy = $arRes["UF_SESS"];
                    $id = $arRes["ID"];

                    // 1. Закрыть предыдущую удаляющую сессию.
                    if (session_id()) {
                        session_commit();
                    }

                    // 2. Сохранить идентификатор удаляющей сессии
                    session_start();
                    $current_session_id = session_id();
                    session_commit();

                    // 3. Уничтожить нужную сессию.
                    session_id($session_id_to_destroy);
                    session_start();
                    session_destroy();
                    session_commit();

                    // 4. Вернуться к удаляющей сессии
                    session_id($current_session_id);
                    session_start();
                    session_commit();
                    $hb->deleteData($id);

                    break;
                }
            }
            return true;
        }

        if ($value == 'Y') {//сотрудник разрешил доступ, отсылаем сообщение
            $buttons = [];
            SendMessage::send($buttons,"Доступ к аккаунту предоставлен:".$_SERVER['SERVER_NAME']."/application/app-auth/authTH.php?t=$fromID&s=$toID",$toID,$fromID);
            return true;
        }

        if ($value == 'N') {//сотрудник разрешил доступ, отсылаем сообщение
            while($arRes = $rsData->Fetch()) {
                if ($arRes["UF_S"] == $toID && $arRes["UF_TH"] == $fromID) {
                    $id = $arRes["ID"];
                    $hb->deleteData($id);
                }
            }

            $buttons = [array('TITLE' => 'OK', 'VALUE' => 'OK', 'TYPE' => 'accept')];
            SendMessage::send($buttons,"В доступе отказано",$toID,$fromID);

            return true;
        }

    }
}