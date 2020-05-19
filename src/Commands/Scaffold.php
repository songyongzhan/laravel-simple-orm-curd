<?php

namespace Songyz\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Lumen\Application as LumenApplication;

/**
 * Class Scaffold
 * @package Songyz\Commands
 * @author songyz <574482856@qq.com>
 * @date 2020/5/19 16:27
 */
class Scaffold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'songyz:scaffold
    {--del_module= : delete module name}
    {--module= : create module name}
    {--only= : only create}
    {--force : Overwrite any existing files}';

    // 带有等号的 可以输入值
    // {--action=create : action-name}  带有默认值
    //不带有等号的，是一个选项

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'laravel-simple-orm-curd scaffold create command';

    private $connection;

    private $tableName;

    private $className;

    /** @var 项目根目录 */
    private $basePath;

    /** @var 目录分割符号 */
    const DS = DIRECTORY_SEPARATOR;

    private $fileDescription = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->basePath = base_path();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //删除
        if ($this->option('del_module')) {
            $this->delModule($this->option('del_module'));
            return;
        }

        $className = $this->option('module');
        if (empty($className)) {
            $className = $this->ask('请输入要生成的类名:', '');
        }

        if (empty($className)) {
            $this->warn("要生成的类名不能为空");
            exit;
        }
        $this->className = ucfirst($className);
        $this->question('要生成的类名是:' . $this->className);

        $this->fileDescription = $fileDescription = $this->ask('请简单描述-只支持英文:', '');
        $this->question('描述信息信息是:' . $fileDescription);

        $connection = $this->ask('请输入Model关联的连接 Connection:', '');
        if (empty($connection)) {
            $connection = 'mysql';
        }

        $this->connection = $connection;

        $this->info('connection:' . $connection);

        $tableName = $this->ask('请输入类对应的表名(可以为空):', '');

        if (empty($tableName)) {
            $tableName = $className;
        }

        $this->tableName = $tableName = Str::snake($tableName);

        $this->info('Model关联的表是:' . $tableName);

        $only = $this->option('only');
        if (empty($only)) {

            $this->createController();

            $this->createModel();

            $this->createManager();

            $this->createService();

            $this->createRoute();
        } else {
            switch ($only) {
                case 'controller':
                    $this->createController();
                    break;
                case 'model':
                    $this->createModel();
                    break;
                case 'manager':
                    $this->createManager();
                    break;
                case 'service':
                    $this->createService();
                    break;
                case 'route':
                    $this->createRoute();
                    break;
                default :
                    $this->alert('命令输入错误');
            }
            return;
        }
    }

    private function createController()
    {

        $controllerStub = <<<'TOT'
<?php

namespace --namespace--;

use Illuminate\Http\Request;
use Songyz\Core\Controller as ApiController;
use --manageNamespace--\--controllerName--Manager;
/**
 * --fileDescription--
 * Class --controllerName--
 * @package App\Http\Controllers
 * @date --datetime--
 */
class --controllerName--Controller extends ApiController
{
    /**
     *
     * add
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @date --datetime--
     */
    public function add(Request $request)
    {
        $param = $request->all();
        $result = $this->getManager()->add($param);

        return $this->showJson($result);
    }

    /**
     *
     * getOne
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @date --datetime--
     */
    public function getOne(Request $request)
    {
        $id = $request->input('id');
        $result = $this->getManager()->getOne($id);

        return $this->showJson($result);
    }

    /**
     *
     * update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @date --datetime--
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $param = $request->except('id');

        $result = $this->getManager()->update($id, $param);

        return $this->showJson($result);
    }

    /**
     *
     * getList
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @date --datetime--
     */
    public function getList(Request $request)
    {
        $rules = [

        ];

        $data = $request->except(['pageNumber', 'pageSize']);
        $where = $this->where($rules, $this->filterData($data));

        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 10);
        $result = $this->getManager()->getList($where, [], $pageSize, $pageNumber);

        return $this->showJson($result);
    }

    /**
     *
     * del
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @date --datetime--
     */
    public function del(Request $request)
    {
        $id = $request->input('id');
        $result = $this->getManager()->del($id);

        return $this->showJson($result);
    }

     /**
     *
     * getManager
     * @return --controllerName--Manager
     *
     * @date --datetime--
     */
    public function getManager()
    {
        return new --controllerName--Manager();
    }
}

TOT;
        $controllerPath = config('songyz_scaffold.controller_path');
        $controllerPath = base_path('app') . str_replace(base_path('app'), '', $controllerPath);

        $currentNameSpace = $this->calculationNameSpace($controllerPath);
        $managerNameSpace = $this->calculationNameSpace(config('songyz_scaffold.manager_path'));

        $content = str_replace([
            '--controllerName--',
            '--datetime--',
            '--namespace--',
            '--manageNamespace--',
            '--fileDescription--'
        ],
            [$this->className, date("Y-m-d H:i:s"), $currentNameSpace, $managerNameSpace, $this->fileDescription],
            $controllerStub);

        //判断如果存在，则提醒用户是否覆盖
        $controllerFile = $controllerPath . self::DS . $this->className . 'Controller.php';
        $createFlag = true;

        if (file_exists($controllerFile) && !$this->option('force')) {
            //提示用户是否替换，如果不 则不生成
            $createFlag = $this->confirm($this->className . 'Controller.php' . ' 文件已存在，是否替换');
        }

        if (!$createFlag) {
            return false;
        }
        $this->createDir($controllerFile);
        $fileSize = file_put_contents($controllerFile, $content);

        $this->info($controllerFile . ' 文件创建成功' . $fileSize);
    }

    private function createManager()
    {

        $managerStub = <<<'TOT'
<?php

namespace --namespace--;

use Songyz\Core\DatabaseManager;
use Songyz\Core\DatabaseService;
use --serviceNamespace--\--managerName--Service;

/**
 * --fileDescription--
 * Class --managerName--Manager
 * @date --datetime--
 */
class --managerName--Manager extends DatabaseManager
{

    public function getService(): DatabaseService
    {
        return new --managerName--Service();
    }
}

TOT;
        $managerPath = config('songyz_scaffold.manager_path');
        $managerPath = base_path('app') . str_replace(base_path('app'), '', $managerPath);

        $currentNameSpace = $this->calculationNameSpace($managerPath);
        $serviceNamespace = $this->calculationNameSpace(config('songyz_scaffold.service_path'));

        $content = str_replace([
            '--managerName--',
            '--datetime--',
            '--namespace--',
            '--serviceNamespace--',
            '--fileDescription--'
        ],
            [$this->className, date("Y-m-d H:i:s"), $currentNameSpace, $serviceNamespace, $this->fileDescription],
            $managerStub);

        //判断如果存在，则提醒用户是否覆盖
        $createFile = $managerPath . self::DS . $this->className . 'Manager.php';
        $createFlag = true;

        if (file_exists($createFile) && !$this->option('force')) {
            //提示用户是否替换，如果不 则不生成
            $createFlag = $this->confirm($this->className . 'Manager.php' . ' 文件已存在，是否替换');
        }

        if (!$createFlag) {
            return false;
        }
        $this->createDir($createFile);
        $fileSize = file_put_contents($createFile, $content);

        $this->info($createFile . ' 文件创建成功' . $fileSize);
    }

    /**
     * 创建service
     * createService
     * @return bool
     *
     */
    private function createService()
    {
        $serviceStub = <<<'TOT'
<?php

namespace --namespace--;

use Songyz\Core\DatabaseService;
use Songyz\Core\Model;
use --modelNamespace--\--serviceName--Model;

/**
 * --fileDescription--
 * Class --serviceName--Service
 * @date --datetime--
 */
class --serviceName--Service extends DatabaseService {
    
    //主键
    protected $primaryId = 'id';

    /**
     * @inheritDoc
     */
    public function getModel(): Model
    {
        return new --serviceName--Model();
    }
}

TOT;
        $servicePath = config('songyz_scaffold.service_path');
        $servicePath = base_path('app') . str_replace(base_path('app'), '', $servicePath);

        $modelNamespace = $this->calculationNameSpace(config('songyz_scaffold.model_path'));
        $currentNameSpace = $this->calculationNameSpace($servicePath);

        $content = str_replace([
            '--serviceName--',
            '--datetime--',
            '--namespace--',
            '--modelNamespace--',
            '--fileDescription--'
        ],
            [$this->className, date("Y-m-d H:i:s"), $currentNameSpace, $modelNamespace, $this->fileDescription],
            $serviceStub);

        //判断如果存在，则提醒用户是否覆盖
        $createFile = $servicePath . self::DS . $this->className . 'Service.php';
        $createFlag = true;

        if (file_exists($createFile) && !$this->option('force')) {
            //提示用户是否替换，如果不 则不生成
            $createFlag = $this->confirm($this->className . 'Service.php' . ' 文件已存在，是否替换');
        }

        if (!$createFlag) {
            return false;
        }
        $this->createDir($createFile);
        $fileSize = file_put_contents($createFile, $content);

        $this->info($createFile . ' 文件创建成功' . $fileSize);
    }

    /**
     * 创建model
     * createModel
     * @return bool
     *
     */
    private function createModel()
    {
        $modelSub = <<<'TOT'
<?php

namespace --namespace--;

use Songyz\Core\Model;

/**
 * --fileDescription--
 * Class --modelName--Model
 * @date --datetime--
 */
class --modelName--Model extends Model
{

    protected $table='--tableName--';
    
    const CREATED_AT = '--created_at--';
    const UPDATED_AT = '--updated_at--';

    protected $connection = '--connectionName--';
}

TOT;
        $modelPath = config('songyz_scaffold.model_path');
        $modelPath = base_path('app') . str_replace(base_path('app'), '', $modelPath);

        $createdAt = config('songyz_scaffold.model_create_at');
        $updateAt = config('songyz_scaffold.model_updated_at');
        empty($createdAt) && $createdAt = 'created_at';
        empty($updateAt) && $updateAt = 'updated_at';

        $currentNameSpace = $this->calculationNameSpace($modelPath);

        $content = str_replace([
            '--modelName--',
            '--datetime--',
            '--namespace--',
            '--connectionName--',
            '--fileDescription--',
            '--tableName--',
            '--created_at--',
            '--updated_at--',
        ],
            [
                $this->className,
                date("Y-m-d H:i:s"),
                $currentNameSpace,
                $this->connection,
                $this->fileDescription,
                $this->tableName,
                $createdAt,
                $updateAt
            ],
            $modelSub);

        //判断如果存在，则提醒用户是否覆盖
        $createFile = $modelPath . self::DS . $this->className . 'Model.php';
        $createFlag = true;

        if (file_exists($createFile) && !$this->option('force')) {
            //提示用户是否替换，如果不 则不生成
            $createFlag = $this->confirm($this->className . 'Model.php' . ' 文件已存在，是否替换');
        }

        if (!$createFlag) {
            return false;
        }

        $this->createDir($createFile);
        $fileSize = file_put_contents($createFile, $content);

        $this->info($createFile . ' 文件创建成功' . $fileSize);
    }

    private function createRoute()
    {

        $controllerNamespace = $this->calculationNameSpace(config('songyz_scaffold.controller_path'));
        //生成的脚本放在哪个文件里  api.php   web.php 默认是web
        //将整个文件读取过来，通过正则匹配，如果存在 就不写入了，不存在的话，就写入
        $replace = 'App\Http\Controllers';
        $routeNamespace = str_replace($replace, '', $controllerNamespace);
        $routeMaps = [
            'getList',
            'del',
            'add',
            'update',
            'getOne'
        ];

        $controller = (empty($routeNamespace) ? '' : ltrim($routeNamespace,
                    '\\') . '\\') . $this->className . 'Controller';

        $routes = [];
        if ($this->getLaravel() instanceof LumenApplication) {
            foreach ($routeMaps as $r) {
                $routes[] = '$router' . "->post('" . lcfirst($this->className) . "/{$r}', '{$controller}@{$r}');";
            }
        } else {
            foreach ($routeMaps as $r) {
                $routes[] = "Route::post('" . lcfirst($this->className) . "/{$r}', '{$controller}@{$r}');";
            }
        }

        $routeFile = config('songyz_scaffold.route_file');
        if (!file_exists($routeFile)) {
            $routeFile = base_path('routes' . self::DS . 'web.php');
        }
        $this->createDir($routeFile);
        file_put_contents($routeFile, "\n" . implode("\n", $routes), FILE_APPEND);
    }

    /**
     * 根据文件创建目录
     * createDir
     * @param $createFile
     *
     */
    private function createDir($createFile)
    {
        $path = dirname($createFile);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * 根据路径计算命名空间
     * calculationNameSpace
     *
     * @date 2020/5/7 21:04
     * @param $path
     * @return string
     */
    private function calculationNameSpace($path)
    {
        //计算根目录 app path
        $basePath = base_path('app');
        $tempPath = str_replace(['/', '\\'], self::DS, str_replace($basePath, '', $path));
        return "App" . self::DS . trim($tempPath, self::DS);
    }

    /**
     * 删除创建的模块
     * delModule
     * @param $module
     *
     * @date 2020/5/7 22:32
     */
    private function delModule($module)
    {

        $path = config('songyz_scaffold.controller_path');
        $path = base_path('app') . str_replace(base_path('app'), '', $path);
        $file = $path . self::DS . ucfirst($module) . 'Controller.php';
        file_exists($file) && unlink($file) && $this->info($file . ' 删除成功');

        $path = config('songyz_scaffold.service_path');
        $path = base_path('app') . str_replace(base_path('app'), '', $path);
        $file = $path . self::DS . ucfirst($module) . 'Service.php';
        file_exists($file) && unlink($file) && $this->info($file . ' 删除成功');

        $path = config('songyz_scaffold.manager_path');
        $path = base_path('app') . str_replace(base_path('app'), '', $path);
        $file = $path . self::DS . ucfirst($module) . 'Manager.php';
        file_exists($file) && unlink($file) && $this->info($file . ' 删除成功');

        $path = config('songyz_scaffold.model_path');
        $path = base_path('app') . str_replace(base_path('app'), '', $path);
        $file = $path . self::DS . ucfirst($module) . 'Model.php';
        file_exists($file) && unlink($file) && $this->info($file . ' 删除成功');

        $this->info($this->option('del_module') . '模块删除成功');
    }

}
