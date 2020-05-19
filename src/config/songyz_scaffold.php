<?php

/**
 * curd 配置文件
 */

return [
    'default_page' => Songyz\Library\DefaultPage::class,
    'controller_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers'),
    'manager_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Managers'),
    'service_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Services'),
    'model_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Models'),
    'route_file' => base_path('routes' . DIRECTORY_SEPARATOR . 'api.php'), //默认存放在api.php
    'model_create_at' => 'created_at',
    'model_updated_at' => 'updated_at',
];
