<?php

namespace Songyz\Simple\Orm\Core;

use Songyz\Simple\Orm\Traits\SnakeCamelChange;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * 核心Model类
 * Class Model
 * @author songyz <574482856@qq.com>
 *
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    use SnakeCamelChange;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /** @var bool 下换线转驼峰 */
    protected $underlineToHump = true;

    /** @var array 拒绝填充的字段 */
    protected $guarded = [];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fill($attributes);
    }

    /**
     * 支持驼峰获取数值
     *
     * getAttribute
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key) {
        if ($this->underlineToHump && (strpos($key, '_') === false) && !array_key_exists($key, $this->attributes)) {
            $key = Str::snake($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * 填充数据字段转换
     * fill
     * @param array $attributes
     * @return \Songyz\Simple\Orm\Core\Model
     *
     * @date 2021/5/28 10:42
     */
    public function fill(array $attributes)
    {
        if ($this->underlineToHump) {
            $attributes = $this->snake($attributes);
        }

        return parent::fill($attributes);
    }
    /**
     * 下划线转驼峰
     * getArrayableAttributes
     * @return array
     */
    protected function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();
        if (!$this->underlineToHump) {
            return $attributes;
        }
        $newAttributes = [];
        foreach ($attributes as $k => $v) {
            $newAttributes[Str::camel($k)] = is_null($v) ? '' : $v;
        }

        return $newAttributes;
    }

    /**
     * 小驼峰转换为下划线
     * setRawAttributes
     * @param array $attributes
     * @param bool $sync
     * @return \Illuminate\Database\Eloquent\Model
     *
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        if (!$this->underlineToHump) {
            return parent::setRawAttributes($attributes, $sync);
        }

        $newAttributes = [];
        foreach ($attributes as $k => $v) {
            $newAttributes[Str::snake($k)] = $v;
        }

        return parent::setRawAttributes($newAttributes, $sync);
    }

    /**
     * 设置搜索条件
     * getQuery
     * @param array $where
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getQuery(array $where = [])
    {
        $query = self::query();

        if ($where) {
            foreach ($where as $key => $val) {
                if (is_array($val) && isset($val['field'])) {
                    $query->where(Str::snake($val['field']), $val['operator'] ?? '=', $val['val'],
                        $val['condition'] ?? 'AND');
                }
                elseif (is_array($val)) {
                    $query->where(Str::snake($key), $val['0'] ?? '=', $val['1'] ?? '', $val['condition'] ?? 'AND');
                }
                else {
                    $query->where(Str::snake($key), '=', $val);
                }
            }
        }

        return $query;
    }

    /**
     * 清空数据库数据表不存在的字段
     * filterToDBData
     * @param $data
     * @param string $table
     * @param bool $keyMap 是否keymap 如果不是则将data转换为key=val
     * @return array
     */
    public function filterFiled($data, $table = '', $keyMap = true)
    {
        $keyMap || $data = array_combine($data, $data);

        $collection = collect($data);
        $fields = $this->getTableField($table);
        $intersect = $collection->intersectByKeys(
            array_combine($fields, $fields)
        );
        $result = $intersect->all();
        $keyMap || $result = array_values($result);

        return $result;
    }

    /**
     * 获取当前表字段
     * getTableField
     * @param string $table
     * @return mixed
     *
     */
    public function getTableField($table = '')
    {
        $table || $table = $this->getTable();
        return Schema::connection($this->connection)->getColumnListing($table);
    }
}
