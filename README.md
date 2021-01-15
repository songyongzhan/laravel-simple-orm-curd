# Laravel-simple-orm-curd

简单、实用、curd接口生成、脚手架

## 1、引入composer

```she

composer require songyz/laravel-simple-orm-curd

```

## 2、加载服务

* Laravel

  打开`config/app.php` 文件，找到  `providers` 数组中添加以下代码

  ```php
  Songyz\Simple\Orm\Providers\ScaffoldConfigPublishProvider::class
  ```

* Lumen

  打开 `bootstrap/app.php` ，添加

  ```php
  $app->register(Songyz\Simple\Orm\Providers\ScaffoldConfigPublishProvider::class);
  ```

## 3、加载配置文件

* Laravel

  ```php
  php artisan vendor:publish --provider="Songyz\Simple\Orm\Providers\ScaffoldConfigPublishProvider" 
  ```

* Lumen

  复制 `\vendor\songyz\laravel-simple-orm-curd\src\config\songyz_scaffold.php` 文件，放在`config` 目录下。

 `songyz_scaffold.php` 文件，具体信息如下：

```php
return [
    'default_page' => '', //orm默认使用的分类
    'controller_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers'), //controller存放位置
    'manager_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Managers'), //manager存放位置
    'service_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Services'), //service 存放位置
    'model_path' => base_path('app' . DIRECTORY_SEPARATOR . 'Models'), //model 存放位置
    'route_file' => base_path('routes' . DIRECTORY_SEPARATOR . 'api.php'), //生成curd路由存放位置
    'model_create_at'=>'created_at', //创建自动插入当前时间字段
    'model_updated_at'=>'updated_at', //更新时自动更新的时间

];

```

## 4、使用命令创建模块

* 1、生成模块

  ```shell
  E:\phpStudy\WWW\laravel-test>php artisan songyz:scaffold
  
   请输入要生成的类名: []:
   > Goods
  
  要生成的类名是:Goods
  
   请简单描述-只支持英文: []:
   > shop
  
  描述信息信息是:shop
  
   请输入Model关联的连接 Connection: []:
   >
  
  connection:mysql
  
   请输入类对应的表名(可以为空): []:
   >
  
  Model关联的表是:goods
  E:\phpStudy\WWW\laravel-test\app\Http\Controllers\GoodsController.php 文件创建成功2478
  E:\phpStudy\WWW\laravel-test\app\Models\GoodsModel.php 文件创建成功300
  E:\phpStudy\WWW\laravel-test\app\Managers\GoodsManager.php 文件创建成功335
  E:\phpStudy\WWW\laravel-test\app\Services\GoodsService.php 文件创建成功400
  ```

  api.php 自动生成路由，

  ```php
  
  Route::post('goods/getList', 'GoodsController@getList');
  Route::post('goods/del', 'GoodsController@del');
  Route::post('goods/add', 'GoodsController@add');
  Route::post('goods/update', 'GoodsController@update');
  Route::post('goods/getOne', 'GoodsController@getOne');
  ```

  生成的路由，未采用restFull模式，为了简化、方便，所以采用全是post方式，您可以修改请求方式，`get` `any` 等方式。

* 2、生成模块 带有参数 --force

  默认文件存在，会提示是否被覆盖，添加上`--force` 参数后将自动覆盖，请谨慎选择。

  

* 3、生成指定的文件，使用 `--only` 指定。`--only` 允许的类型是：`controller` 、`manager` 、`service` 、`model` 、`route` 其他格式不被允许

  ```she
  php artisan songyz:scaffold --only=services --module=Goods
  ```

  如果文件存在，会提示是否覆盖文件。

  

* 4、删除模块

  ```she
  php artisan songyz:scaffold --del_module=Goods
  ```

  > 注：删除不可恢复、删除时不会清除路由，请手动删除

  

## 5、curd接口测试

* 1、新增接口 `/api/goods/add` 

  返回结果

  ```json
  {"code":"0","message":"成功","data":{"id":1}}
  ```

* 2、列表查询接口  `/api/goods/getList`

  ```json
  {"code":"0","message":"成功","data":{"pageNumber":1,"pageSize":10,"totalPages":1,"totalCount":1,"results":[{"id":1,"title":"苹果手机6 plus","nav":1,"createdAt":"2020-05-19 16:09:34","deletedAt":"","updatedAt":"2020-05-19 16:10:42"}]}}
  ```

* 3、更新接口 `/api/goods/update`

  ```json
  {"code":"0","message":"成功","data":{"id":"1"}}
  ```

* 4、查询单个数据`/api/goods/getOne`

  ```json
  {"code":"0","message":"成功","data":{"id":1,"title":"苹果手机6 plus 加强版","nav":1,"createdAt":"2020-05-19 16:09:34","deletedAt":"","updatedAt":"2020-05-19 16:10:42"}}
  ```

  

* 5、删除 `/api/goods/del`

  ```json
  {"code":"0","message":"成功","data":{"id":"1"}}
  ```

以上方法均为post请求。

6、其他帮助文档，请移步 ....
