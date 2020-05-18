<?php

/**
 * curd 配置文件
 */
return [
    'default_page' => '',
    'controller_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Emr'),
    'manager_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Managers'),
    'service_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Services'),
    'model_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Models'),
    'route_file' => base_path('routes' . DIRECTORY_SEPARATOR . 'web.php'),
];
