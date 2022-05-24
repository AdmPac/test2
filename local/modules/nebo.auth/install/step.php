<?php
use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}

Loc::loadMessages(__FILE__);

if ($errorException = $APPLICATION->getException()) {
    // ошибка при установке модуля
    CAdminMessage::showMessage(
        Loc::getMessage('NEBO_AUTH_INSTALL_FAILED').': '.$errorException->GetString()
    );
} else {
    // модуль успешно установлен
    CAdminMessage::showNote(
        Loc::getMessage('NEBO_AUTH_INSTALL_SUCCESS')
    );
}
?>

<form action="<?= $APPLICATION->getCurPage(); ?>"> <!-- Кнопка возврата к списку модулей -->
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>" />
    <input type="submit" value="<?= Loc::getMessage('NEBO_AUTH_RETURN_MODULES'); ?>">
</form>