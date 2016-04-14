<?php
/**
 * 数组类函数测试
 * @author 周仕林<875199116@qq.com> 2016-04-02
 */
class ArraysTest extends PHPUnit_Framework_TestCase {
    public function testFirst()
    {
        $this->assertEquals(1, __::first(array(1, 2, 3)));
        $this->assertEquals(array(), __::first(array(1, 2, 3), 0));
        $this->assertEquals(array(1, 2), __::first(array(1,2,3), 2), 'can pass an index to first');
    }

    public function testMax()
    {
        $this->assertEquals(3, __::max(array(1,2,3)), 'can perform a regular max');
        $this->assertEquals(1, __::max(array(1,2,3), function($num) { return -$num; }), 'can performa a computation-based max');
    }
}