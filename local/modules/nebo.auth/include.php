<?php
CModule::AddAutoloadClasses(
    "nebo.auth",
    array(
        "\\Nebo\\Auth\\SendMessage" => "lib/SendMessage.php",
        "\\Nebo\\Auth\\HelperHB" => "lib/HelperHB.php",
        "\\Nebo\\Auth\\EventMessages" => "lib/EventMessages.php",
        )
);
