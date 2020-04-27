<?php


namespace Test;


use MrCron\Runner\Job;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{

    public function testDueDateByMinute()
    {
        $dev = new Job([
            "id" => "test",
            "cron"=> "* * * * *",
            "urls" => []
        ]);
        $this->assertTrue($dev->isToDue(0, strtotime("2020-01-01T00:00:00Z")));
        $this->assertTrue($dev->isToDue(0, strtotime("2020-01-01T00:01:00Z")));
    }

    public function testDueDateByHour()
    {
        $dev = new Job([
            "id" => "test",
            "cron"=> "0 * * * *",
            "urls" => []
        ]);
        $this->assertTrue($dev->isToDue(0, strtotime("2020-01-01T00:00:00Z")));
        $this->assertFalse($dev->isToDue(0, strtotime("2020-01-01T00:01:00Z")));
    }

    public function testDueDateByDayOfMonth()
    {
        $dev = new Job([
            "id" => "test",
            "cron"=> "0 0 5 * *",
            "urls" => []
        ]);
        $this->assertTrue($dev->isToDue(0, strtotime("2020-01-05T00:00:00Z")));
        $this->assertFalse($dev->isToDue(0, strtotime("2020-01-01T00:00:00Z")));
    }

    public function testDueDateByDayOfMonthOfYear()
    {
        $dev = new Job([
            "id" => "test",
            "cron"=> "0 0 1 1 *",
            "urls" => []
        ]);
        $this->assertTrue($dev->isToDue(0, strtotime("2020-01-01T00:00:00Z")));
        $this->assertFalse($dev->isToDue(0, strtotime("2020-02-01T00:00:00Z")));
    }

    public function testDueDateByDayOfWeekday()
    {
        $dev = new Job([
            "id" => "test",
            "cron"=> "0 0 * * 0",
            "urls" => []
        ]);
        // Execute every Sunday at 00:00

        $this->assertTrue($dev->isToDue(0, strtotime("2020-01-05T00:00:00Z")));
        $this->assertFalse($dev->isToDue(0, strtotime("2020-02-06T00:00:00Z")));
    }
}
