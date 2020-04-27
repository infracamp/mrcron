<?php


namespace MrCron\Runner;


use Phore\Core\Exception\InvalidDataException;

class Job
{

    public $id;
    public $cron;
    public $cronTimes = [
        "min" => [],
        "hour" => [],
        "day_of_month" => [],
        "month" => [],
        "day_of_week" => []
    ];

    public $urls = [];

    public function transformCronEntry (string $line, $max) : array
    {
        $times = explode(",", $line);
        $ret = [];

        if ($line === "*") {
            for ($i=0; $i<=$max; $i++)
                $ret[] = $i;
        } else {
            foreach ($times as $time) {
                if (trim($time) === "")
                    continue;
                $ret[] = (int)$time;
            }
        }


        return $ret;
    }

    public function __construct(array $data)
    {
        $this->id = phore_pluck("id", $data);
        $this->cron = phore_pluck("cron", $data);
        if ( ! preg_match ("/^([0-9\\/\\*,]+)\s+([0-9\\/\\*,]+)\s+([0-9\\/\\*,]+)\s+([0-9\\/\\*,]+)\s+([0-9\\/\\*,]+)$/", $this->cron, $matches)) {
            throw new InvalidDataException("Cannot parse cron-line: '{$this->cron}'");
        }
        $this->cronTimes["min"] = $this->transformCronEntry($matches[1], 59);
        $this->cronTimes["hour"] = $this->transformCronEntry($matches[2], 23);
        $this->cronTimes["day_of_month"] = $this->transformCronEntry($matches[3], 31);
        $this->cronTimes["month"] = $this->transformCronEntry($matches[4], 12);
        $this->cronTimes["day_of_week"] = $this->transformCronEntry($matches[5], 6);

        $this->urls = phore_pluck("urls", $data);
    }


    /**
     * Return true if the job should be executed right now.
     *
     * @param int $lastExecTs
     * @return bool
     */
    public function isToDue(int $lastExecTs, int $curTs) : bool
    {

        return (in_array((int)gmdate("i", $curTs), $this->cronTimes["min"])
            && in_array((int)gmdate("G", $curTs), $this->cronTimes["hour"])
            && in_array((int)gmdate("j", $curTs), $this->cronTimes["day_of_month"])
            && in_array((int)gmdate("n", $curTs), $this->cronTimes["month"])
            && in_array((int)gmdate("w", $curTs), $this->cronTimes["day_of_week"])
        );
    }


}
