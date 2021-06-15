<?php

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ORM\Query\Query;
use Phinx\Migration\AbstractMigration;
use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main\Config\Option;
use Bitrix\Highloadblock;

/**
 * Class AddUserOrderIblock
 * Миграция для инфоблока Заказ клиента
 *
 * @property $IblockId int
 */
class AddUserOrderIblock extends AbstractMigration
{

    protected $iBlockId;

    /**
     * Установить миграцию
     */
    public function up()
    {
        $this->setSettings();

        $this->IBlockID = $this->createIBlock();

        if (!$this->IBlockID) {
            echo "Error";
            return false;
        }

        $this->createFieldsIBlock($this->IBlockID);
    }

    /**
     * Откатить миграцию
     */
    public function down()
    {
        $this->setSettings();

        $this->deleteIBlock();
    }

    /**
     * Подключение необходимых модулей
     */
    protected function setSettings()
    {
        $_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

        Loader::includeModule("iblock");
    }

    /**
     * Создание инфоблока Заказ клиента
     *
     * @return bool
     */
    protected function createIBlock()
    {
        $iBlock = new CIBlock;

        $arFields = [
            "ACTIVE" => "Y",
            "VERSION" => 2,
            "NAME" => "Заказ клиента",
            "CODE" => "client-order",
            "LIST_PAGE_URL" => "#SITE_DIR#/client-order/",
            "DETAIL_PAGE_URL" => "#SITE_DIR#/client-order/",
            'CANONICAL_PAGE_URL' => '#SITE_DIR#/client-order/',
            "INDEX_ELEMENT" => "Y",
            "WORKFLOW" => "Y",
            "IBLOCK_TYPE_ID" => "test",
            "SITE_ID" => array("s1", "is"),
        ];
        $iBlockId = $iBlock->Add($arFields);

        return $iBlockId;
    }

    /**
     * @param int $iBlockId
     * @return array
     */
    protected function setFields(int $iBlockId): array
    {
        return [
            [
                "NAME" => "Название",
                "ACTIVE" => "Y",
                "SORT" => 10,
                "CODE" => "NAME",
                "PROPERTY_TYPE" => "S",
                "FILTRABLE" => "Y",
                "IBLOCK_ID" => $iBlockId
            ],
            [
                "NAME" => "Дата",
                "ACTIVE" => "Y",
                "SORT" => 20,
                "CODE" => "DATE",
                "PROPERTY_TYPE" => "S",
                "USER_TYPE" => "DateTime",
                "FILTRABLE" => "Y",
                "IBLOCK_ID" => $iBlockId,
            ],
            [
                "NAME" => "Город",
                "ACTIVE" => "Y",
                "SORT" => 30,
                "CODE" => "CITY",
                "PROPERTY_TYPE" => "S",
                "USER_TYPE" => "directory",
                "LIST_TYPE" => "L",
                "FILTRABLE" => "Y",
                "IBLOCK_ID" => $iBlockId,
                "USER_TYPE_SETTINGS" => ["size" => "1", "width" => "0", "group" => "N", "multiple" => "N", "TABLE_NAME" => "developer_information"]
            ],
            [
                "NAME" => "Пользователь",
                "ACTIVE" => "Y",
                "SORT" => 40,
                "CODE" => "USER",
                "PROPERTY_TYPE" => "S",
                "USER_TYPE" => "UserID",
                "FILTRABLE" => "Y",
                "IBLOCK_ID" => $iBlockId,
            ],
        ];
    }

    /**
     * Добавление свойств для нового инфоблока
     *
     * @param $iBlockId int
     */
    protected function createFieldsIBlock(int $iBlockId)
    {
        $ibp = new CIBlockProperty;

        foreach ($this->setFields($iBlockId) as $fields) {
            $propId = $ibp->Add($fields);
            if ($propId > 0) {
                echo "Добавлено свойство " . $fields["NAME"];
                continue;
            }

            echo "Ошибка добавления свойства " . $fields["NAME"];
        }
    }

    /**
     * Удаление инфоблока
     */
    protected function deleteIBlock()
    {
        $this->IBlockID = Iblock\IblockTable::getList([
            "select" => ["ID"],
            "filter" => ["=CODE" => "client-order"],
        ])->fetch()["ID"];

        if (!\CIBlock::Delete($this->IBlockID)) {
            echo "Error: can't delete iblock<br>";
        }
    }
}