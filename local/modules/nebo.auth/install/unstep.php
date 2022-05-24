<?php
/*
 * Файл local/modules/scrollup/install/unstep.php
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()){
    return;
}

if ($errorException = $APPLICATION->getException()) {
    // ошибка при удалении модуля
    echo CAdminMessage::showMessage(
        Loc::getMessage('NEBO_AUTH_UNINSTALL_FAILED') . ': ' . $errorException->GetString()
    );
}
?>
<form action="<?= $APPLICATION->getCurPage(); ?>"> <!-- Кнопка возврата к списку модулей -->
    <?=bitrix_sessid_post();?>
    <input type="hidden" name="step" value="2" />
    <input type="hidden" name="id" value="nebo.auth" />
    <input type="hidden" name="uninstall" value="Y" /><!-- БЕЗ ЭТОГО НЕ ПЕРЕЙДЕМ НА СЛЕДУЮЩИЙ ШАГ УДАЛЕНИЯ, А ПЕРЕЙДЕМ В СПИСОК МОДУЛЕЙ -->
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>" />
    <p><?php echo CAdminMessage::showMessage(Loc::getMessage('NEBO_AUTH_DELETE_DB_WARNING')) ?></p>
    <p><input type="checkbox" name="deletedata" id="deletedata" value="Y" checked><label><?php echo Loc::getMessage("NEBO_AUTH_DELETE_DB") ?></label></p>
    <input type="submit" value="<?= Loc::getMessage('NEBO_AUTH_Y'); ?>">

</form>