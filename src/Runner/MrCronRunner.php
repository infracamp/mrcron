<?php


namespace MrCron\Runner;


use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Phore\HttpClient\PhoreHttpAsyncQueue;
use Phore\HttpClient\PhoreHttpResponse;
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
        foreach ($job->getRequests() as $request) {
            $queue->queue($request)->then(
                function(PhoreHttpResponse $success) {
                    phore_out("Job success on url '{$success->getRequest()->getUrl()}': " . $success->getBody());
                    $this->log->notice("Job success on url '{$success->getRequest()->getUrl()}': " . $success->getBody());
                },
                function (PhoreHttpRequestException $ex) {
                    phore_out("Job failed on url: " . $ex->getMessage());
                    $this->log->warning("Job failed on url: " . $ex->getMessage());
                }
            );
        }
    }

    public function run(array $scrapeUrls, bool $singleShot=false)
    {
        $lastMinute = (int)gmdate("i");
        while (true) {
            phore_out("Waiting for next minute...");
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
            if ($singleShot) {
                // For development: Single shot mode (By adding -s to command line)
                sleep(1);
                phore_out("Single shot mode: Exiting after one loop.");
                break;
            }


        }

    }
}
