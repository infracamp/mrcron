<?php


namespace MrCron\Runner;


class FileJobLocker implements JobLocker
{

    /**
     * @inheritDoc
     */
    public function getLastExecTime(string $jobId): int
    {

    }

    public function setLastExecTime(string $jobId, int $ts)
    {

    }
}
