<?php
/**
 * PHP仿写underscore.js
 * 参考Underscore.php(https://github.com/brianhaveri/Underscore.php)
 * @author 周仕林<875199116@qq.com> 2016-04-02
 */

/**
 * 生成__对象
 * @param null $item
 * @return __
 */
function __($item = null)
{
    $__ = new __();
    if (func_num_args() > 0) $__->_wrapped = $item;

    return $__;
}

/**
 * Class __
 */
class __
{
    /**
     * each函数
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function each($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        //判断是否有数据集合
        if (is_null($collection)) return self::_wrap(null);

        $collection = (array)self::_collection($collection);
        if (count($collection) === 0) return self::_wrap(null);

        foreach ($collection as $k => $v) {
            //调用自定义迭代器函数
            call_user_func($iterator, $v, $k, $collection);
        }

        return self::_wrap(null);
    }

    /**
     * 通过迭代器映射每个元素最终返回数据
     * map alias: collect
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return array|mixed
     */
    public function collect($collection = null, $iterator = null)
    {
        return self::map($collection, $iterator);
    }

    public function map($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        //判断是否有数据集合
        if (is_null($collection)) return self::_wrap(array());

        $collection = (array)self::_collection($collection);
        if (count($collection) === 0) return self::_wrap(array());

        $return = array();
        foreach ($collection as $k => $v) {
            $return[] = call_user_func($iterator, $v, $k, $collection);
        }

        return $return;
    }

    /**
     * array_reduce() 函数用回调函数迭代地将数组简化为单一的值
     * reduce alias: foldl,inject
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @param null $memo 初始值
     * @return mixed
     * @throws Exception
     */
    public function foldl($collection = null, $iterator = null, $memo = null)
    {
        return self::reduce($collection, $iterator, $memo);
    }

    public function inject($collection = null, $iterator = null, $memo = null)
    {
        return self::reduce($collection, $iterator, $memo);
    }

    public function reduce($collection = null, $iterator = null, $memo = null)
    {
        list($collection, $iterator, $memo) = self::_wrapArgs(func_get_args(), 3);

        if (!is_object($collection) && !is_array($collection)) {
            if (is_null($collection)) {
                throw new Exception('Invalid Object');
            } else {
                return self::_wrap($memo);
            }
        }

        return self::_wrap(array_reduce($collection, $iterator, $memo));
    }

    /**
     * array_reduce() 函数用回调函数迭代地将数组简化为单一的值
     * reduceRight alias: foldr
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @param null $memo 初始值
     * @return mixed
     * @throws Exception
     */
    public function foldr($collection = null, $iterator = null, $memo = null)
    {
        return self::reduceRight($collection, $iterator, $memo);
    }

    public function reduceRight($collection = null, $iterator = null, $memo = null)
    {
        list($collection, $iterator, $memo) = self::_wrapArgs(func_get_args(), 3);

        if (!is_object($collection) && !is_array($collection)) {
            if (is_null($collection)) {
                throw new Exception('Invalid Object');
            } else {
                return self::_wrap($memo);
            }
        }

        krsort($collection);

        //考虑
        $__ = new self();

        return self::_wrap($__->reduce($collection, $iterator, $memo));
    }

    /**
     * 根据迭代器查找某元素(第一个先匹配true)
     * find alias: detect
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function detect($collection = null, $iterator = null)
    {
        return self::find($collection, $iterator);
    }

    public function find($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        foreach ($collection as $value) {
            if (call_user_func($iterator, $value)) {
                return $value;
            }
        }

        return self::_wrap(false);
    }

    /**
     * 根据条件查询
     * filter alias: select
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function select($collection = null, $iterator = null)
    {
        return self::filter($collection, $iterator);
    }

    public function filter($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $return = array();
        foreach ($collection as $value) {
            if (call_user_func($iterator, $value)) {
                $return[] = $value;
            }
        }

        return self::_wrap($return);
    }

    /**
     * 返回不满足迭代器条件的数据
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function reject($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $return = array();
        foreach ($collection as $value) {
            if (!call_user_func($iterator, $value)) {
                $return[] = $value;
            }
        }

        return self::_wrap($return);
    }

    /**
     * 所有数值都匹配迭代器则返回true,反之false
     * all alias:every
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return bool
     */
    public function every($collection = null, $iterator = null)
    {
        return self::all($collection, $iterator);
    }

    public function all($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $__ = new self();
        if (!is_null($iterator)) {
            $collection = $__->map($collection, $iterator);
        }

        $collection = (array)$collection;
        if (count($collection) == 0) return true;

        return self::_wrap(is_bool(array_search(false, $collection, false)));
    }

    /**
     * 数组中任何数值都匹配迭代器则返回true,反之false
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return bool|mixed
     */
    public function any($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $__ = new self();
        if (!is_null($iterator)) {
            $collection = $__->map($collection, $iterator);
        }

        $collection = (array)$collection;
        if (count($collection) == 0) return false;

        return self::_wrap(is_int(array_search(true, $collection, false)));
    }

    /**
     * 判断集合是否包含指定值
     * includ alias contains
     * @param null $collection 集合
     * @param null $value 指定值
     * @return null
     */
    public function contains($collection = null, $value = null)
    {
        return self::includ($collection, $value);
    }

    public function includ($collection = null, $value = null)
    {
        list($collection, $value) = self::_wrapArgs(func_get_args(), 2);

        $collection = (array)self::_collection($collection);

        return self::_wrap(array_search($value, $collection, true));
    }

    /**
     *
     * @param null $collection
     * @param null $functionName
     * @TODO
     */
    public function invoke($collection = null, $functionName = null)
    {
    }

    /**
     * 返回指定属性的值
     * @param null $collection 集合
     * @param null $property 属性名
     * @return array
     */
    public function pluck($collection = null, $property = null)
    {
        list($collection, $property) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $return = array();
        foreach ($collection as $item) {
            foreach ($item as $k => $value) {
                if ($k == $property) {
                    $return[] = $value;
                }
            }
        }

        return $return;
    }

    /**
     * 返回最大值
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function max($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        if (is_null($iterator)) return self::_wrap(max($collection));

        $results = array();
        foreach ($collection as $key => $item) {
            $results[$key] = $iterator($item);
        }

        arsort($results);

        $__ = new self();
        $first_key = $__->first(array_keys($results));

        return $collection[$first_key];
    }

    /**
     * 返回最小值
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function min($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        if (is_null($iterator)) return self::_wrap(min($collection));

        $results = array();
        foreach ($collection as $key => $item) {
            $results[$key] = $iterator($item);
        }

        asort($results);

        $__ = new self();
        $first_key = $__->first(array_keys($results));

        return $collection[$first_key];
    }

    /**
     * 分组统计
     * @param null $collection 集合
     * @param null $iterator 迭代器
     * @return mixed
     */
    public function groupBy($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $result = array();
        $collection = (array)$collection;
        foreach ($collection as $k => $v) {
            $key = is_callable($iterator) ? $iterator($v, $k) : $v[$iterator];
            if (!array_key_exists($key, $result)) {
                $result[$key] = array();
            }

            $result[$key][] = $v;
        }

        return self::_wrap($result);
    }

    /**
     * 排序
     * @param null $collection 集合
     * @param null $iterator 前n个元素
     * @return mixed
     */
    public function sortBy($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $result = array();
        foreach ($collection as $k => $v) {
            $result[$k] = $iterator($v);
        }

        asort($result);
        foreach ($result as $k => $v) {
            $result[$k] = $collection[$k];
        }

        return self::_wrap(array_values($result));
    }

    /**
     * 返回插入的位置
     * @param null $collection 集合
     * @param null $value 插入值
     * @param null $iterator 前n个元素
     * @return mixed
     */
    public function sortedIndex($collection = null, $value = null, $iterator = null)
    {
        list($collection, $value, $iterator) = self::_wrapArgs(func_get_args(), 3);

        $collection = (array)self::_collection($collection);
        $calculated_value = (!is_null($iterator)) ? $iterator($value) : $value;

        while (count($collection) > 1) {
            $midpoint = floor(count($collection) / 2);
            $midpoint_values = array_slice($collection, $midpoint, 1);
            $midpoint_value = $midpoint_values[0];
            $midpoint_calculated_value = (!is_null($iterator)) ? $iterator($midpoint_value) : $midpoint_value;
            $collection = $calculated_value < $midpoint_calculated_value ? array_slice($collection, 0, $midpoint, true) : array_slice($collection, $midpoint, null, true);
        }

        $keys = array_keys($collection);

        return self::_wrap(current($keys) + 1);
    }

    /**
     * 打乱集合
     * @param null $collection 集合
     * @return mixed
     */
    public function shuffle($collection = null)
    {
        list($collection) = self::_wrapArgs(func_get_args(), 1);

        $collection = (array)self::_collection($collection);

        shuffle($collection);

        return $this->_wrap($collection);
    }

    /**
     * 转化为数组
     * @param null $collection 集合
     * @return array
     */
    public function toArray($collection = null)
    {
        return (array)$collection;
    }

    /**
     * 计算集合大小
     * @param null $collection
     * @return mixed
     */
    public function size($collection = null)
    {
        list($collection) = self::_wrapArgs(func_get_args(), 1);

        $collection = self::_collection($collection);

        return self::_wrap(count((array)$collection));
    }

    /**
     * 获取数组的第一个元素，若存在参数n返回前n个元素
     * @param null $collection 集合
     * @param null $n 前n个元素
     * @return mixed
     */
    public function head($collection = null, $n = null)
    {
        return self::first($collection, $n);
    }

    public function first($collection = null, $n = null)
    {
        list($collection, $n) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);
        if ($n === 0) return self::_wrap(array());
        if (is_null($n)) return self::_wrap(current(array_slice($collection, 0, 1)));

        return self::_wrap(array_slice($collection, 0, $n, true));
    }

    /**
     * 返回数组中除了最后一个元素外的其他全部元素。 在arguments对象上特别有用。传递 n参数将从结果中排除从最后一个开始的n个元素（注：排除数组后面的 n 个元素）。
     * @param null $collection 集合
     * @param null $n n
     * @return mixed
     */
    public function initial($collection = null, $n = null)
    {
        list($collection, $n) = self::_wrapArgs(func_get_args(), 2);

        $collection = (array)self::_collection($collection);

        if (is_null($n)) $n = 1;
        $first_index = count($collection) - $n;

        $__ = new self();

        return self::_wrap($__::first($collection, $first_index));
    }

    /**
     * 返回数组中除了第一个元素外的其他全部元素。传递 index 参数将返回从index开始的剩余所有元素
     * @param null $collection 集合
     * @param null $n n
     * @return mixed
     */
    public function tail($collection = null, $n = null)
    {
        return self::rest($collection, $n);
    }

    public function rest($collection = null, $n = null)
    {
        list($collection, $n) = self::_wrapArgs(func_get_args(), 2);

        $collection = (array)self::_collection($collection);

        if (is_null($n)) $n = 1;

        return self::_wrap(array_slice($collection, $n));
    }

    /**
     * 返回array（数组）的最后一个元素。传递 n参数将返回数组中从最后一个元素开始的n个元素（注：返回数组里的后面的n个元素）
     * @param null $collection 集合
     * @param null $n n
     * @return mixed
     */
    public function last($collection = null, $n = null)
    {
        list($collection, $n) = self::_wrapArgs(func_get_args(), 2);

        $collection = (array)self::_collection($collection);

        if ($n === 0) $result = array();
        elseif ($n === 1 || is_null($n)) $result = array_pop($collection);
        else {
            $__ = new self();
            $result = $__::rest($collection, -$n);
        }

        return self::_wrap($result);
    }

    /**
     * 返回一个除去所有false值的 array副本
     * @param null $collection 集合
     * @return mixed
     */
    public function compact($collection = null)
    {
        list($collection) = self::_wrapArgs(func_get_args(), 1);

        $collection = self::_collection($collection);

        $__ = new self();

        return self::_wrap($__::select($collection, function ($num) {
            return (bool)$num;
        }));
    }

    /**
     * 将一个嵌套多层的数组 array（数组） (嵌套可以是任何层数)转换为只有一层的数组。 如果你传递 shallow参数，数组将只减少一维的嵌套。
     * @param null $collection 集合
     * @param null $shallow 是否只减少一维
     * @return mixed
     */
    public function flatten($collection = null, $shallow = null)
    {
        list($collection, $shallow) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $result = array();
        foreach ($collection as $item) {
            if (is_array($item)) {
                $__ = new self();
                $result = array_merge($result, $shallow ? $item : $__::flatten($item));
            } else {
                $result[] = $item;
            }
        }

        return self::_wrap($result);
    }

    /**
     * 返回一个删除所有values值后的 array副本
     * @param null $collection
     * @param null $values
     * @return mixed
     */
    public function without($collection = null, $values = null)
    {
        $args = self::_wrapArgs(func_get_args(), 1);
        $collection = $args[0];
        $collection = self::_collection($collection);

        $num_args = count($args);
        if ($num_args === 1) return self::_wrap($collection);
        if (count($num_args) === 0) return self::_wrap($collection);

        $__ = new self();
        $values = $__::rest($args);
        foreach ($values as $value) {
            $remove_keys = array_keys($collection, $value, true);
            foreach ($remove_keys as $remove_key) {
                unset($collection[$remove_key]);
            }
        }

        return self::_wrap($collection);
    }

    /**
     * 返回 array去重后的副本, 使用 === 做相等测试. 如果您确定 array 已经排序, 那么给 isSorted并没有影响(跟underscore.js不同). 如果要处理对象元素, 传参 iterator 来获取要对比的属性.
     * @param null $collection
     * @param null $isSorted
     * @param null $iterator
     * @return mixed
     */
    public function unique($collection = null, $isSorted = null, $iterator = null)
    {
        return self::uniq($collection, $isSorted, $iterator);
    }

    public function uniq($collection = null, $isSorted = null, $iterator = null)
    {
        list($collection, $isSorted, $shallow) = self::_wrapArgs(func_get_args(), 3);

        $collection = self::_collection($collection);
        if (count($collection) === 0) return self::_wrap(array());

        $result = array();
        $calculated = array();
        foreach ($collection as $item) {
            $value = !is_null($iterator) ? $iterator($item) : $item;
            if (is_bool(array_search($value, $calculated, true))) {
                $calculated[] = $value;
                $result[] = $value;
            }
        }

        return self::_wrap($result);
    }

    /**
     * 合并多个数组
     * @param null $array
     * @return mixed
     */
    public function union($array = null)
    {
        $arrays = self::_wrapArgs(func_get_args(), 1);

        if (count($arrays) === 1) return self::_wrap($arrays);

        return self::_wrap(array_values(array_unique(call_user_func_array('array_merge', $arrays))));
    }

    /**
     * 交集
     * @param null $array
     * @return mixed
     */
    public function intersection($array = null)
    {
        $arrays = self::_wrapArgs(func_get_args(), 1);

        if (count($arrays) === 1) return self::_wrap($arrays);

        $__ = new self();
        $return = $__::first($arrays);
        foreach ($__::rest($arrays) as $item) {
            $return = array_intersect($return, $item);
        }

        return self::_wrap($return);
    }

    /**
     * 只存在第一个数组中,但是不在其他数组中
     * @param null $array
     * @return mixed
     */
    public function difference($array = null)
    {
        $arrays = self::_wrapArgs(func_get_args(), 1);

        return self::_wrap(array_values(call_user_func_array('array_diff', $arrays)));
    }

    public function range()
    {
    }

    /**
     * 可自定义函数来扩展该类
     */
    private $_mixins = array();
    public function mixin($functions = null)
    {
        list($functions) = self::_wrapArgs(func_get_args(), 1);

        $mixins =& self::getInstance()->_mixins;
        foreach ($functions as $name => $function) {
            $mixins[$name] = $function;
        }

        return self::_wrap(null);
    }

    /**
     * 可以实现静态调用扩展函数
     * @param string $name 函数名
     * @param mixed $arguments 参数
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $mixins =& self::getInstance()->_mixins;
        $arguments = self::_wrapArgs($arguments);

        return call_user_func($mixins[$name], $arguments);
    }

    /**
     * 可以实现非静态下调用扩展函数
     * @param string $name 函数名
     * @param mixed $arguments 参数
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $mixins =& self::getInstance()->_mixins;
        $arguments = self::_wrapArgs($arguments);

        return call_user_func($mixins[$name], $arguments);
    }

    /**
     * 实现单例模式
     */
    private static $_instance;
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public $_wrapped = null; //用来实现chain用法中数据在方法之间传递

    /**
     * 该类里所有的方法返回值必须通过_wrap函数
     * @param $value
     * @return mixed
     */
    private function _wrap($value)
    {
        return $value;
    }

    /**
     * 该类里所有的方法取参数必须通过_wrapArgs函数
     * @param $caller_args
     * @param null $num_args
     * @return array
     */
    private function _wrapArgs($caller_args, $num_args = null)
    {
        $num_args = is_null($num_args) ? count($caller_args) - 1 : $num_args;

        $filled_args = array();
        if (isset($this) && isset($this->_wrapped)) {
            $filled_args[] = $this->_wrapped;
        }

        if (count($caller_args)) {
            foreach ($caller_args as $caller_arg) {
                $filled_args[] = $caller_arg;
            }
        }

        return array_pad($filled_args, $num_args, null);
    }

    /**
     * 过滤集合格式(必须是数组或者对象),如不是强制转化为数组
     * @param $collection
     * @return array
     */
    private function _collection($collection)
    {
        return (!is_array($collection) && !is_object($collection)) ? str_split((string)$collection) : $collection;
    }
}