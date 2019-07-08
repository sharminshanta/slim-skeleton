<?php

use Sharminshanta\Web\Accounts\Controller\DefaultController;

$app->get('/', DefaultController::class . ":defaultView");
