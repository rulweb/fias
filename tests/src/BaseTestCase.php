<?php

namespace marvin255\fias\tests;

abstract class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @return \Faker\Generator
     */
    public function faker(): \Faker\Generator
    {
        if ($this->faker === null) {
            $this->faker = \Faker\Factory::create();
        }

        return $this->faker;
    }
}
