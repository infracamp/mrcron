<?php


namespace MrCron\Runner;


interface JobLocker
{

    /**
     * Returns the epoch timestamp the job was executed the last time or
     * 0 if it was not jet executed at all.
     *
     * @param string $jobId
     * @return int
     */
    public function getLastExecTime(string $jobId) : int;

    public function setLastExecTime(string $jobId, int $ts);

}
