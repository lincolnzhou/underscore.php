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

    public function testMin() {
        // from js
        $this->assertEquals(1, __::min(array(1,2,3)), 'can perform a regular min');
        $this->assertEquals(3, __::min(array(1,2,3), function($num) { return -$num; }), 'can performa a computation-based max');
    }

    public function testInitial()
    {
        $this->assertEquals(array(1, 2, 3), __::initial(array(1, 2, 3, 4)));
        $this->assertEquals(array(1, 2), __::initial(array(1, 2, 3, 4), 2));
        $this->assertEquals(array(), __::initial(array(1, 2, 3, 4), 4));
        $this->assertEquals(array(1), __::initial(array(1, 2, 3, 4), 7));
        $this->assertEquals(array(1), __::initial(array(1, 2, 3), 5), 'works with surplus n');
    }

    public function testRest()
    {
        $this->assertEquals(array(2, 3, 4), __::rest(array(1, 2, 3, 4)));
        $this->assertEquals(array(2, 3, 4), __::tail(array(1, 2, 3, 4)));

        $this->assertEquals(array(2, 3, 4), __::rest(array(1, 2, 3, 4), 1));
        $this->assertEquals(array(3, 4), __::rest(array(1, 2, 3, 4), 2));
        $this->assertEquals(array(), __::rest(array(1, 2, 3, 4), 5));
        $this->assertEquals(array(3, 4), __::rest(array(1, 2, 3, 4), -2));
    }

    public function testLast()
    {
        $this->assertEquals(array(), __::last(array(1, 2, 3, 4), 0));
        $this->assertEquals(4, __::last(array(1, 2, 3, 4)));
        $this->assertEquals(array(3, 4), __::last(array(1, 2, 3, 4), 2));
    }

    public function testCompact()
    {
        $vals = array(1, 0, false, 'false', null);

        $this->assertEquals(array(1, 'false'), __::compact($vals));
    }
}