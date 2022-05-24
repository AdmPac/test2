<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('CHK_EVENT', true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/bx_root.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\EventManager;

if(!\Bitrix\Main\Loader::includeModule('nebo.auth')){
    die("Установите модуль nebo.auth");
}


global $APPLICATION;
$APPLICATION->ShowHead();//для Работы BX js
$GLOBALS["APPLICATION"]->ShowCSS();

function authTF($id){
    global $USER;
    $myId = $USER->getID();
    if($USER->Authorize($id)){
        $USER->Authorize($myId);
        return true;
    }
}

$rsUsers = CUser::GetList(($by="id"), ($order="desc"));

while($arItem = $rsUsers->GetNext())
{
    if(!authTF($arItem['ID'])) continue;
    $list[] = ['data'=>
        [
            'LOGIN'=>$arItem['LOGIN'],
            'NAME'=>$arItem['NAME']." ".$arItem['LAST_NAME'],
            'ID'=>$arItem['ID'],

        ]
    ];
}

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => 'grid_id',
        'COLUMNS' => [
            ['id' => 'LOGIN', 'name' => 'Почта сотрудника', 'default' => true],
            ['id' => 'NAME', 'name' => 'Имя сотрудника', 'default' => true],
            ['id' => 'ID', 'name' => 'ID сотрудника', 'default' => true],
        ],
        'ROWS' => $list,
        'SHOW_ROW_CHECKBOXES' => false,
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU'     => false,
        'SHOW_GRID_SETTINGS_MENU'   => false,
        'SHOW_NAVIGATION_PANEL'     => false,
        'SHOW_PAGINATION'           => false,
        'SHOW_SELECTED_COUNTER'     => false,
        'SHOW_TOTAL_COUNTER'        => false,
        'SHOW_PAGESIZE'             => false,
        'SHOW_ACTION_PANEL'         => false,
    ]
);
?>
<script type="text/javascript" src="js/script.js">

</script>
