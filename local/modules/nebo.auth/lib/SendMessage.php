<?php
namespace Nebo\Auth;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/bx_root.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
class SendMessage{
    static public function send($arrButton,$msg,$from,$to)
    {
        if(\Bitrix\Main\Loader::includeModule('im')) {
            $s = array(
                // получатель
                "TO_USER_ID" => $to,
                // отправитель
                "FROM_USER_ID" => $from,
                // тип уведомления
                "NOTIFY_TYPE" => IM_NOTIFY_CONFIRM,
                // модуль запросивший отправку уведомления
                "NOTIFY_MODULE" => "calendar",
                // символьный тэг для группировки (будет выведено только одно сообщение), если это не требуется - не задаем параметр
                "NOTIFY_TAG" => "$to|" . $from,
                // текст уведомления на сайте (доступен html и бб-коды)
                "NOTIFY_MESSAGE" => $msg,
                // текст уведомления для отправки на почту (или XMPP), если различий нет - не задаем параметр
                "NOTIFY_BUTTONS" => $arrButton,
                // символьный код шаблона отправки письма, если не задавать отправляется шаблоном уведомлений
                "NOTIFY_EMAIL_TEMPLATE" => "CALENDAR_INVITATION",
            );
            \CIMNotify::Add($s);
        }
    }
}