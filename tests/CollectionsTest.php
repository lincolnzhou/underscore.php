<?php

/**
 * 集合类函数测试
 * @author 周仕林<875199116@qq.com> 2016-04-02
 */
class CollectionsTest extends PHPUnit_Framework_TestCase {
    /**
     * 测试each函数
     * @author 周仕林<875199116@qq.com> 2016-04-02
     */
    public function testEach()
    {
        $test =& $this;
        __::each(array(1, 2, 3, 4), function($num, $i) use($test) {
            $test->assertEquals($num, $i + 1, 'each iterators provide value and iteration count');
        });

        $answers = array();
        $obj = (object)array('multiplier' => 5);
        __::each(array(1, 2, 3), function($num) use(&$answers, $obj) {
            $answers[] = $num * $obj->multiplier;
        });
        $test->assertEquals([5, 10, 15], $answers, 'context object property accessed');

        $answers = array();
        $obj = (object)array('one' => 1, 'two' => 2, 'three' => 3);
        __::each($obj, function($value, $key) use(&$answers) {
            $answers[] = $key;
        });
        $test->assertEquals(array('one', 'two', 'three'), $answers, 'iterating over objects works');


    }
}