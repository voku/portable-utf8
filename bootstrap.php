<?php

use voku\helper\Bootup;

Bootup::initAll(); // Enables the portability layer and configures PHP for UTF-8
Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC
