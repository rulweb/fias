<?php

namespace marvin255\fias\tests\pipe;

class FlowTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $param_name = 'param_' . mt_rand();
        $param_value = 'value_' . mt_rand();
        $param_1_name = 'param_1_' . mt_rand();
        $param_1_value = 'value_1_' . mt_rand();

        $flow = new \marvin255\fias\pipe\Flow;

        $flow->set($param_name, $param_value);

        $this->assertSame(
            $flow,
            $flow->set($param_1_name, $param_1_value)
        );
        $this->assertSame(
            $param_1_value,
            $flow->get($param_1_name)
        );
        $this->assertSame(
            $param_value,
            $flow->get($param_name)
        );
        $this->assertSame(
            null,
            $flow->get('empty_param_' . mt_rand())
        );
    }

    public function testSetParamNameException()
    {
        $name = 'test_exception_ ' . mt_rand();
        $flow = new \marvin255\fias\pipe\Flow;
        $this->setExpectedException('\InvalidArgumentException', $name);
        $flow->set($name, mt_rand());
    }

    public function testSetAll()
    {
        $param_name = 'param_' . mt_rand();
        $param_value = 'value_' . mt_rand();
        $param_1_name = 'param_1_' . mt_rand();
        $param_1_value = 'value_1_' . mt_rand();
        $param_2_name = 'param_2_' . mt_rand();
        $param_2_value = 'value_2_' . mt_rand();

        $flow = new \marvin255\fias\pipe\Flow;

        $this->assertSame(
            [],
            $flow->getAll()
        );

        $flow->set($param_2_name, $param_2_value);

        $this->assertSame(
            $flow,
            $flow->setAll([
                $param_name => $param_value,
                $param_1_name => $param_1_value,
            ])
        );
        $this->assertSame(
            [
                $param_name => $param_value,
                $param_1_name => $param_1_value,
            ],
            $flow->getAll()
        );
    }
}
