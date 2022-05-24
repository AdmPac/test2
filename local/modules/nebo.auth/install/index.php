<?php
//echo require_once "/lib/class/EventMessage.php";

use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable as HB;


Loader::includeModule('crm');
Loader::includeModule('im');
Loader::IncludeModule('highloadblock');

use Nebo\Auth\HelperHB;

Loc::loadMessages(__FILE__);


class nebo_auth extends CModule {
    public $hlblock_id = 0;

    public function __construct() {
        if (is_file(__DIR__.'/version.php')) {
            include_once(__DIR__.'/version.php');
            $this->MODULE_ID           = 'nebo.auth';
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME         = Loc::getMessage('NEBO_AUTH_NAME');
            $this->MODULE_DESCRIPTION  = Loc::getMessage('NEBO_AUTH_DESCRIPTION');
        }
    }
//
//
    public function unistallDB(){
//        $hb = new HelperHB("AuthTH");
        $hlb = HB::getList([
            'filter' => ['=NAME' => "AuthTH"]
        ])->fetch();
        $this->hlblock_id = $hlb['ID'];

        HB::delete($this->hlblock_id);
    }

    public function installDB()
    {
        $highloadBlockData = array ( 'NAME' => 'AuthTH', 'TABLE_NAME' => 'auth_th' );
        $result = HB::add($highloadBlockData);
        $this->hlblock_id = $result->getId();

        $userTypeEntity    = new CUserTypeEntity();

        $userTypeData    = array(
            'ENTITY_ID'         => 'HLBLOCK_'.$this->hlblock_id,
            'FIELD_NAME'        => 'UF_SESS',
            'USER_TYPE_ID'      => 'string',
            'XML_ID'            => '',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_FILTER'       => 'N',
            'SHOW_IN_LIST'      => '',
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => '',
            'SETTINGS'          => array(
                'DEFAULT_VALUE' => '',
                'SIZE'          => '20',
                'ROWS'          => '1',
                'MIN_LENGTH'    => '0',
                'MAX_LENGTH'    => '0',
                'REGEXP'        => '',
            ),
        );

        $userTypeData2    = array(
            'ENTITY_ID'         => 'HLBLOCK_'.$this->hlblock_id,
            'FIELD_NAME'        => 'UF_TH',
            'USER_TYPE_ID'      => 'integer',
            'XML_ID'            => '',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_FILTER'       => 'N',
            'SHOW_IN_LIST'      => '',
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => '',
            'SETTINGS'          => array(
                'DEFAULT_VALUE' => '',
                'SIZE'          => '20',
                'ROWS'          => '1',
                'MIN_LENGTH'    => '0',
                'MAX_LENGTH'    => '0',
                'REGEXP'        => '',
            ),
        );
        $userTypeData3    = array(
            'ENTITY_ID'         => 'HLBLOCK_'.$this->hlblock_id,
            'FIELD_NAME'        => 'UF_S',
            'USER_TYPE_ID'      => 'integer',
            'XML_ID'            => '',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_FILTER'       => 'N',
            'SHOW_IN_LIST'      => '',
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => '',
            'SETTINGS'          => array(
                'DEFAULT_VALUE' => '',
                'SIZE'          => '20',
                'ROWS'          => '1',
                'MIN_LENGTH'    => '0',
                'MAX_LENGTH'    => '0',
                'REGEXP'        => '',
            ),
        );
        $userTypeData4    = array(
            'ENTITY_ID'         => 'HLBLOCK_'.$this->hlblock_id,
            'FIELD_NAME'        => 'UF_DATE',
            'USER_TYPE_ID'      => 'integer',
            'XML_ID'            => '',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_FILTER'       => 'N',
            'SHOW_IN_LIST'      => '',
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => '',
            'SETTINGS'          => array(
                'DEFAULT_VALUE' => '',
                'SIZE'          => '20',
                'ROWS'          => '1',
                'MIN_LENGTH'    => '0',
                'MAX_LENGTH'    => '0',
                'REGEXP'        => '',
            ),
        );
        $userTypeEntity->Add($userTypeData);
        $userTypeEntity->Add($userTypeData2);
        $userTypeEntity->Add($userTypeData3);
        $userTypeEntity->Add($userTypeData4);
    }

    public function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        $hlb = HB::getList([
            'filter' => ['=NAME' => "AuthTH"]
        ])->fetch();
        $hlblock_id = $hlb['ID'];

        if(!$hlblock_id) {// если HB не удалили при удалении модуля
            $this->installDB();
        }
        RegisterModule("nebo.auth");

        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler('im', 'OnAfterConfirmNotify', $this->MODULE_ID, '\\Nebo\\Auth\\EventMessages', 'EventHandlers');

        $APPLICATION->IncludeAdminFile(Loc::getMessage('NEBO_AUTH_INSTALL_TITLE'), $DOCUMENT_ROOT."/local/modules/nebo.auth/install/step.php");
    }

    public function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        if($request['step']<2){
            $APPLICATION->IncludeAdminFile(Loc::getMessage('NEBO_AUTH_DELETE_DB_WARNING'), $DOCUMENT_ROOT."/local/modules/nebo.auth/install/unstep.php");
        }elseif($request['step']==2) {
            if ($request['deletedata'] == 'Y') {
                $this->unistallDB();
            }

            UnRegisterModule("nebo.auth");


            $APPLICATION->IncludeAdminFile(Loc::getMessage('NEBO_AUTH_UNINSTALL_FAILED'), $DOCUMENT_ROOT . "/local/modules/nebo.auth/install/unstep1.php");

            $eventManager = EventManager::getInstance();
            $eventManager->unRegisterEventHandler('im', 'OnAfterConfirmNotify', $this->MODULE_ID, '\\Nebo\\Auth\\EventMessages', 'EventHandlers');
        }
    }
}
    
