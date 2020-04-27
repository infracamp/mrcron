<?php


namespace Test;


use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{

    public function testRunOnMockRoute()
    {
        phore_exec ("sudo rm /tmp/_test_run || true");
        phore_exec("/opt/bin/mrcron -s 'http://localhost/mock/mrcron.json'");
        $this->assertTrue(file_exists("/tmp/_test_run"));
    }

}
