<?php
/**
 * 数组类函数测试
 * @author 周仕林<875199116@qq.com> 2016-04-02
 */
class ArraysTest extends PHPUnit_Framework_TestCase {
    public function testFirst()
    {
         $this->assertEquals(1, __::first(array(1, 2, 3)));
    }
}