<?php
//Класс(обертка HB) формирует объект HB и добавляет/изменяет данные
namespace Nebo\Auth;

\Bitrix\Main\Loader::includeModule('highloadblock'); // подключаем модуль HL блоков
use Bitrix\Highloadblock\HighloadBlockTable as HB;

class HelperHB{
    private $hlblock_id;//id HB
    public $entity_data_class;
    public function __construct($name){//находит id HB по имени
        $hlb = HB::getList([
            'filter' => ['=NAME' => $name]
        ])->fetch();
        $this->hlblock_id = $hlb['ID'];
    }

    public function getObjHB(){//генерирует и возвращает объект HB для дальнейшей работы
        $hlblock = HB::getById( $this->hlblock_id )->fetch(); // получаем объект вашего HL блока
        $entity = HB::compileEntity( $hlblock ); // получаем рабочую сущность
        $this->entity_data_class = $entity->getDataClass(); // получаем экземпляр класса
        $entity_table_name = $hlblock['TABLE_NAME']; // присваиваем переменной название HL таблицы
        $sTableID = 'tbl_'.$entity_table_name; // добавляем префикс и окончательно формируем название
        $o = $this->entity_data_class;

        $rsData = $o::getList(array(
            "select" => ['*'],
            "filter" => [],
            "limit" => '0', //ограничим выборку пятью элементами
            "order" => []
        ));
        return new \CDBResult($rsData, $sTableID);
    }

    public function addData($data,$id=""){//добавляет или обновляет запись в таблице
        if($id){//обновление
            $this->entity_data_class::update($id,$data);
            return;
        }
        $this->entity_data_class::add($data);
    }

    public function deleteData($id){//удаление по id
        $this->entity_data_class::delete($id);
    }
}