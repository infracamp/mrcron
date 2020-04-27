<?php


namespace MrCron\Runner;


use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Phore\HttpClient\PhoreHttpAsyncQueue;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;

class MrCronRunner
{

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = $logger;
    }


    /**
     * @param array $scrapeUrls
     * @return Job[]
     */
    public function _retrieveConfig(array $scrapeUrls) : array
    {
        $jobs = [];
        foreach ($scrapeUrls as $scrapeUrl) {
            if (trim ($scrapeUrl) === "")
                continue;
            phore_out("Retrieving url '$scrapeUrl'...");
            try {
                $data = phore_http_request($scrapeUrl)->send()->getBodyJson();
                foreach (phore_pluck("jobs", $data, []) as $jobData) {
                    $jobs[] = new Job($jobData);
                }
            } catch (PhoreHttpRequestException $e) {
                phore_out("Can't pull job data from '$scrapeUrl': " . $e->getMessage());
                $this->log->alert("Can't pull job data from '$scrapeUrl': " . $e->getMessage());
                continue;
            } catch (Exception $e) {
                phore_out("Error in job-data from '$scrapeUrl': " . $e->getMessage());
                $this->log->alert("Error in job-data from '$scrapeUrl': " . $e->getMessage());
                continue;
            }
        }
        return $jobs;
    }


    public function runJob(Job $job, PhoreHttpAsyncQueue $queue)
    {
        phore_out("Running job '{$job->id}'");
    }

    public function run(array $scrapeUrls)
    {
        $lastMinute = (int)gmdate("i");
        while (true) {
            while ($lastMinute === (int)gmdate("i")) {
                sleep (1);
            }
            $lastMinute = (int)gmdate("i");
            $ts = time();

            $jobs = $this->_retrieveConfig($scrapeUrls);
            $jobsToRun = [];
            foreach ($jobs as $job) {
                if ( ! $job->isToDue(0, $ts))
                    continue;
                $jobsToRun[] = $job;
            }

            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new \Exception("Cannot fork!");
            } else if ($pid) {
                // Master process
            } else {
                // Child Process
                $queue = new PhoreHttpAsyncQueue();

                foreach ($jobsToRun as $job) {
                    $this->runJob($job, $queue);
                }
                $queue->wait();
                exit (0);
            }

        }

    }
}
