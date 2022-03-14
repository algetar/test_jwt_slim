<?php

declare(strict_types=1);

namespace App\view\layout;

use App\Facades\Html;

/** @var string $content */
/** @var string $title */

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Html::encode($title) ?></title>
    </head>
    <body>
        <?=$content?>
    </body>
</html>