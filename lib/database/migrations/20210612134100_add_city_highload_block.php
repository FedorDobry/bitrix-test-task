<?php

use Phinx\Migration\AbstractMigration;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Iblock;
use Bitrix\Main\Config\Option;
use Bitrix\Highloadblock as HL;

/**
 * Class AddHousingComplexHighloadBlock
 *
 * @property $iBlockId int
 */
class AddCityHighloadBlock extends AbstractMigration
{
    protected $iBlockId;

    /**
     * Установить миграцию
     */
    public function up()
    {
        $this->setSettings();

        $this->iBlockID = $this->createIBlock();
        if (!$this->iBlockID) {
            echo "Error";
            return false;
        }

        $this->createFieldsIBlock();

        $this->createFieldsIBlock();
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
        Loader::includeModule("highloadblock");

    }

    /**
     * @return int
     */
    protected function createIBlock(): int
    {
        $arLangs = array(
            'ru' => 'Город',
            'en' => 'City '
        );

        $result = HL\HighloadBlockTable::add(array(
            'NAME' => 'City',
            'TABLE_NAME' => 'city',
        ));

        if ($result->isSuccess()) {
            $iBlockId = $result->getId();
            foreach ($arLangs as $lang_key => $lang_val) {
                HL\HighloadBlockLangTable::add(array(
                    'ID' => $iBlockId,
                    'LID' => $lang_key,
                    'NAME' => $lang_val
                ));
            }

            return $iBlockId;
        }

        $errors = $result->getErrorMessages();
        var_dump($errors);

        return 0;
    }

    /**
     * @return array
     */
    protected function setFields(): array
    {
        return [
            [
                'ENTITY_ID' => 'HLBLOCK_' . $this->iBlockID,
                'FIELD_NAME' => 'UF_NAME',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'NAME',
                'SORT' => 100,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => [
                    'SIZE' => 20,
                    'DEFAULT_VALUE' => ''
                ],
                'EDIT_FORM_LABEL' => [
                    'en' => 'Name',
                    'ru' => 'Наименование'
                ],
                'LIST_COLUMN_LABEL' => [
                    'en' => 'Name',
                    'ru' => 'Наименование'
                ],
                'LIST_FILTER_LABEL' => [
                    'en' => 'Name',
                    'ru' => 'Наименование'
                ]
            ],
            [
                'ENTITY_ID' => 'HLBLOCK_' . $this->iBlockID,
                'FIELD_NAME' => 'UF_XML_ID',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'XML_ID',
                'SORT' => 200,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => [
                    'SIZE' => 20,
                    'DEFAULT_VALUE' => ''
                ],
                'EDIT_FORM_LABEL' => [
                    'en' => 'xml_id',
                    'ru' => 'Символьный код'
                ],
                'LIST_COLUMN_LABEL' => [
                    'en' => 'xml_id',
                    'ru' => 'Символьный код'
                ],
                'LIST_FILTER_LABEL' => [
                    'en' => 'xml_id',
                    'ru' => 'Символьный код'
                ]
            ],
        ];
    }

    /**
     * Добавление свойств для нового инфоблока
     *
     * @return bool
     */
    protected function createFieldsIBlock()
    {
        $ibp = new CUserTypeEntity;

        foreach ($this->setFields() as $fields) {
            $propId = $ibp->Add($fields);
            if ($propId > 0) {
                echo "Добавлено свойство " . $fields["XML_ID"];
                continue;
            }

            echo "Ошибка добавления свойства " . $fields["XML_ID"];
        }
    }

    /**
     * Удаление инфоблока
     */
    protected function deleteIBlock()
    {
        $filter = array(
            'select' => array('ID'),
            'filter' => array('=NAME' => "City")
        );
        $iBlockId = HL\HighloadBlockTable::getList($filter)->fetch();
        if (is_array($iBlockId) && !empty($iBlockId)) {
            HL\HighloadBlockTable::delete($iBlockId['ID']);
        }
    }
}