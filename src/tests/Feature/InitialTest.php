<?php

namespace Ljozwiak\Gus\Tests\Feature;

use Orchestra\Testbench\TestCase;

class InitialTest extends TestCase
{
    /** @test */
    public function first_test(): void
    {
        $this->visit('/')
            ->see('Laravel');
    }
}
