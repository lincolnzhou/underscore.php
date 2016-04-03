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
    public function collect($collection = null, $iterator = null) { return self::map($collection, $iterator); }
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
    public function foldl($collection = null, $iterator = null, $memo = null) { return self::reduce($collection, $iterator, $memo); }
    public function inject($collection = null, $iterator = null, $memo = null) { return self::reduce($collection, $iterator, $memo); }
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
    public function foldr($collection = null, $iterator = null, $memo = null) { return self::reduceRight($collection, $iterator, $memo); }
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
    public function detect($collection = null, $iterator = null) { return self::find($collection, $iterator); }
    public function find($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        foreach ($collection as $value) {
            if (call_user_func($iterator, $value)) { return $value; }
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
    public function select($collection = null, $iterator = null) { return self::filter($collection, $iterator); }
    public function filter($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        $collection = self::_collection($collection);

        $return = array();
        foreach ($collection as $value) {
            if (call_user_func($iterator, $value)) { $return[] = $value; }
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
            if (!call_user_func($iterator, $value)) { $return[] = $value; }
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
    public function every($collection = null, $iterator = null) { return self::all($collection, $iterator); }
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
     *
     * @param null $collection
     * @param null $functionName
     * @TODO
     */
    public function invoke($collection = null, $functionName = null) {}

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
     * 判断集合是否包含指定值
     * includ alias contains
     * @param null $collection 集合
     * @param null $value 指定值
     * @return null
     */
    public function contains($collection = null, $value = null) { return self::includ($collection, $value); }
    public function includ($collection = null, $value = null)
    {
        list($collection, $value) = self::_wrapArgs(func_get_args(), 2);

        $collection = (array)self::_collection($collection);

        return self::_wrap(array_search($value, $collection, true));
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