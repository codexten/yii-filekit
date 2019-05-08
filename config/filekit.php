<?php
/**
 * Created by PhpStorm.
 * User: jomon
 * Date: 26/2/19
 * Time: 9:31 PM
 */

use codexten\yii\filekit\controllers\FileStorageController;
use trntv\filekit\widget\Upload;

return [
    'controllerMap' => [
        'file-storage' => [
            'class' => FileStorageController::class,
        ],
    ],
    'container' => [
        'definitions' => [
            Upload::class => [
                'url' => ['/file-storage/upload'],
                'maxFileSize' => 5000000, // 5 MiB
            ],
        ],
    ],
];