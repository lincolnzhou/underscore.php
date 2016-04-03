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
            $test->assertEquals($num, $i + 1, 'each iterators provide value and iteration count(每个迭代器都提供值和索引)');
        });

        $answers = array();
        $obj = (object)array('multiplier' => 5);
        __::each(array(1, 2, 3), function($num) use(&$answers, $obj) {
            $answers[] = $num * $obj->multiplier;
        });
        $test->assertEquals([5, 10, 15], $answers, 'context object property accessed(对象属性在迭代器里可以被访问)');

        $answers = array();
        $obj = (object)array('one' => 1, 'two' => 2, 'three' => 3);
        __::each($obj, function($value, $key) use(&$answers) {
            $answers[] = $key;
        });
        $test->assertEquals(array('one', 'two', 'three'), $answers, 'iterating over objects works(迭代器中对象同样跟数组一样使用)');

        $answer = null;
        __::each(array(1, 2, 3), function($num, $index, $arr) use (&$answer) {
            if (__::includ($arr, $num)) $answer = true;
        });
        $test->assertTrue($answer, 'can reference the original collection from inside the iterator(迭代器里可以访问原集合)');

        $answer = 0;
        __::each(null, function() use ($answer) {
            $answer++;
        });
        $test->assertEquals(0, $answer, 'handles a null property(可以操作一个null对象)');

        __(array(1, 2, 3, 4))->each(function($num, $i) use($test) {
            $test->assertEquals($num, $i + 1, 'each iterators provide value and iteration count within OO-style call(使用方法构造,每个迭代器同样提供值和索引)');
        });
    }

    public function testMap()
    {
        $this->assertEquals(array(2, 4, 6), __::map(array(1, 2, 3), function($num) {
            return $num * 2;
        }), 'double numbers');

        $ifnull = __::map(null, function() {});
        $this->assertTrue(is_array($ifnull) && count($ifnull) == 0, 'handles a null property');

        $multiplier = 3;
        $func = function($num) use ($multiplier) { return $num * $multiplier; };
        $tripled = __::map(array(1,2,3), $func);
        $this->assertEquals(array(3,6,9), $tripled);

        $this->assertEquals(array(2, 4, 6), __(array(1, 2, 3))->map(function($num) {
            return $num * 2;
        }), 'OO-style doubled numbers');

        $doubled = __::collect(array(1, 2, 3), function($num) { return $num * 2; });
        $this->assertEquals(array(2, 4, 6), $doubled , 'aliased as "collect"');

        $this->assertEquals(array(3, 6, 9), __::map(array('one' => 1, 'two' => 2, 'three' => 3), function($num, $key) { return $num * 3; }));
    }

    public function testReduce()
    {
        $sum = __::reduce(array(1, 2, 3), function ($sum, $num) { return $sum + $num; }, 0);
        $this->assertEquals(6, $sum, 'can sum up an array');

        $context = array('multiplier' => 3);
        $sum = __::reduce(array(1, 2, 3), function ($sum, $num) use ($context) {
            return $sum + $num * $context['multiplier'];
        }, 0);
        $this->assertEquals(18, $sum, 'can reduce with a context object');

        $sum = __::reduce(array(1, 2, 3), function ($sum, $num) { return $sum + $num; });
        $this->assertEquals(6, $sum, 'default initial value');

        $ifnull = null;
        try {
            __::reduce(null, function() {});
        } catch (Exception $e) {
            $ifnull = true;
        }
        $this->assertTrue($ifnull, 'handles a null (without initial value) properly');

        $this->assertEquals(138, __::reduce(138, function () {}, 138), 'handles a null (with initial value) properly');

        $sum = __(array(1, 2, 3))->reduce(function ($sum, $num) { return $sum + $num; }, 0);
        $this->assertEquals(6, $sum, 'OO-style reduce');

        $sum = __::inject(array(1, 2, 3), function ($sum, $num) { return $sum + $num; }, 0);
        $this->assertEquals(6, $sum, 'aliased as "inject"');

        $sum = __::foldl(array(1, 2, 3), function ($sum, $num) { return $sum + $num; }, 0);
        $this->assertEquals(6, $sum, 'aliased as "foldl"');
    }

    public function testReduceRight()
    {
        $str = __::reduceRight(array('foo', 'bar', 'barz'), function ($memo, $num) { return $memo . $num; }, '');
        $this->assertEquals('barzbarfoo', $str, 'can perform right folds');

        $ifnull = null;
        try {
            __::reduceRight(null, function() {});
        } catch (Exception $e) {
            $ifnull = true;
        }
        $this->assertTrue($ifnull, 'handles a null (without initial value) properly');

        $this->assertEquals(138, __::reduceRight(138, function () {}, 138), 'handles a null (with initial value) properly');

        $list = __(array('moe','curly','larry'))->reduceRight(function($memo, $str) { return $memo . $str; }, '');
        $this->assertEquals('larrycurlymoe', $list, 'can perform right folds in OO-style');

        $str = __::foldr(array('foo', 'bar', 'barz'), function ($memo, $num) { return $memo . $num; }, '');
        $this->assertEquals('barzbarfoo', $str, 'aliased as "foldr"');

        $str = __::foldr(array('foo', 'bar', 'barz'), function ($memo, $num) { return $memo . $num; });
        $this->assertEquals('barzbarfoo', $str, 'default initial value');
    }

    public function testFind()
    {
        $this->assertEquals(2, __::find(array(1,2,3), function($num) { return $num * 2 === 4; }), 'found the first "2" and broke the loop');
        $this->assertEquals(false, __::find(array(), function() {}));
    }

    public function testFilter()
    {
        $this->assertEquals(array(2, 4, 6), __::filter(array(1, 2, 3, 4, 5, 6), function ($num) { return $num % 2 == 0; }));
        $this->assertEquals(array(2, 4, 6), __::select(array(1, 2, 3, 4, 5, 6), function ($num) { return $num % 2 == 0; }));
    }

    public function testReject()
    {
        $this->assertEquals(array(1, 3), __::reject(array(1,2,3), function($num) { return $num * 2 === 4; }), '');
    }

    public function testAll()
    {
        $this->assertTrue(__::all(array(0, 10, 28), function($num) { return $num % 2 === 0;  }), 'even numbers');
    }

    public function testAny()
    {
        $this->assertTrue(__::any(array(1, 10, 29), function($num) { return $num % 2 === 0; }), 'an even number');
    }

    public function testPluck()
    {
        $arr = array(
            array('name' => 'test', 'email' => '1@qq.com'),
            array('name' => 'test1', 'email' => '2@qq.com'),
            array('name' => 'test2', 'email' => '3@qq.com'),
        );
        $this->assertEquals(array('test', 'test1', 'test2'), __::pluck($arr, 'name'));
    }
}