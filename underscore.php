<?php
/**
 * PHP仿写underscore.js
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
class __ {
    public function each($collection = null, $iterator = null)
    {
        list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);

        //判断是否有数据集合
        if (is_null($collection)) return self::_wrap(null);

        $collection = (array)self::_collection($collection);
        if (count($collection) === 0) return self::_wrap(null);

        foreach ($collection as $k => $v) {
            call_user_func($iterator, $v, $k, $collection);
        }

        return self::_wrap(null);
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