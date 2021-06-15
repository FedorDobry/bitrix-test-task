<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>
<form name="client-order-form" method="POST">

    <select name='selector'>
        <option value='#'>Выберите из списка</option>
        <? foreach ($arResult['DEV_INFO'] as $property): ?>
        <option value="<?= $property['UF_CITY'] ?>"><?= $property['UF_CITY'] ?></option>
        <? endforeach; ?>
    </select>

    <input type="hidden" name="date" value="<?= date('Y-m-d H:i:s', strtotime("+15 minutes")) ?>">

    <input type="hidden" name="user_id" value="<?= $USER->GetID() ?>">

    <input type="submit" value="Отправить">


</form>
<? foreach ($arResult['CLIENT_ORDER'] as $property): ?>
    <div class="object-params">
        <div class="object-param">
            <span class="object-param__name">Название</span>
            <span class="object-param__value"><?= $property['NAME'] ?></span>
        </div>
        <div class="object-param">
            <span class="object-param__name">Город</span>
            <span class="object-param__value"><?= $property['IBLOCK_ELEMENTS_ELEMENT_CLIENT_ORDER_CITY_VALUE'] ?></span>
        </div>
        <div class="object-param">
            <span class="object-param__name">Дата</span>
            <span class="object-param__value"><?= $property['IBLOCK_ELEMENTS_ELEMENT_CLIENT_ORDER_DATE_VALUE'] ?></span>
        </div>
    </div>
<? endforeach; ?>

