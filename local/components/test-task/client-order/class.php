<?

namespace TestTask\Components;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Query;
use \CBitrixComponent;
use CIBlockElement;


/**
 * Class HousingComplex
 * @package Milana\Components
 */
class ClientOrder extends CBitrixComponent
{
    /**
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');

        $arParams = parent::onPrepareComponentParams($arParams);
        $arParams['HBLOCK_ID'] = $this->getCityHighloadBlockId();
        $arParams['IBLOCK_ID'] = $this->getClientOrderIblockId();
        $arParams['CITY'] = $this->request->getPost('selector');
        $arParams['DATE'] = $this->request->getPost('date');
        $arParams['USER_ID'] = $this->request->getPost('user_id');

        return $arParams;
    }

    public function executeComponent(): void
    {

        $this->arResult['DEV_INFO'] = $this->getCityHighloadBlockProperty($this->arParams['HBLOCK_ID']);

        $this->arResult['CLIENT_ORDER'] = $this->getHousingComplexProperty();

        $this->arResult['NAME'] = $this->setClientOrderName($this->arParams['CITY'], $this->arParams['DATE'] , $this->arParams['USER_ID']);

        $this->createClientOrderElement($this->arResult['NAME'], $this->arParams['DATE'], $this->arParams['CITY'], $this->arParams['USER_ID']);

        $this->includeComponentTemplate();

    }

    /**
     * @return int
     */
    protected function getCityHighloadBlockId(): int
    {
        $queryResult = (new Query(HighloadBlockTable::getEntity()))
            ->setSelect([
                'ID'
            ])
            ->setFilter([
                '=NAME' => 'City'
            ])
            ->exec();

        return (int)$queryResult->fetch()['ID'];
    }

    /**
     * @return int
     */
    protected function getClientOrderIblockId(): int
    {
        $queryResult = (new Query(IblockTable::getEntity()))
            ->setSelect([
                'ID'
            ])
            ->setFilter([
                '=CODE' => 'client-order'
            ])
            ->exec();

        return (int)$queryResult->fetch()['ID'];
    }

    /**
     * @param int $hblockId
     * @return array
     */
    protected function getCityHighloadBlockProperty(int $hblockId): array
    {
        $hlblock = HighloadBlockTable::getById($hblockId)->fetch();

        $entity = HighloadBlockTable::compileEntity($hblockId);

        $entity_data_class = $entity->getDataClass();

        $rsData = $entity_data_class::getList([
            'select' => [
                'UF_CITY'
            ],
            'filter' => [
            ]
        ]);

        return $rsData->fetchAll();
    }

    /**
     * @return string
     */
    protected function setClientOrderName($city, $date, $userId): string
    {
        $hashName = hash('ripemd160', $city . $date . $userId);

        return $hashName;
    }

    protected function createClientOrderElement($name, $date, $city, $userId)
    {
        $el = new CIBlockElement;

        $PROP = [];
        $PROP[2] = $date;
        $PROP[3] = $city;
        $PROP[4] = $userId;

        $arLoadProductArray = [
            "MODIFIED_BY"    => 1, // элемент изменен текущим пользователем
            "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
            "IBLOCK_ID"      => 1,
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => "$name",
            "ACTIVE"         => "Y",
            "PREVIEW_TEXT"   => "текст для списка элементов",
            "DETAIL_TEXT"    => "текст для детального просмотра",
        ];

        if($PRODUCT_ID = $el->Add($arLoadProductArray))
            echo "New ID: ".$PRODUCT_ID;
        else
            echo "Error: ".$el->LAST_ERROR;
    }

    /**
     * @return array
     */
    protected function getHousingComplexProperty(): array
    {
        $elementEntity = IblockTable::compileEntity('clientOrder');
        $res =
            (new Query($elementEntity))
            ->setSelect([
                'NAME',
                'CITY',
                'DATE'
            ])
            ->setCacheTtl([
                'ttl' => 3600
            ])
            ->exec();

        return $res->fetchAll();
    }
}