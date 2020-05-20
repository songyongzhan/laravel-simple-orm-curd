<?php

/**
 * curd 配置文件
 * 1、default_page 根据需要，可自定义分页类，
 * 2、controller_path manager_path service_path service_path 定义生成文件保存的位置
 *
 * @author songyz <574482856@qq.com>
 */
return [
    'default_page' => Songyz\Simple\Orm\Library\DefaultPage::class,
    'controller_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers'),
    'manager_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Managers'),
    'service_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Services'),
    'model_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Models'),
    'route_file' => base_path('routes' . DIRECTORY_SEPARATOR . 'web.php'),
    'model_create_at' => 'created_at',
    'model_updated_at' => 'updated_at',
];
