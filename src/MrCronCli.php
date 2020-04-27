<?php


namespace MrCron;


use MrCron\Runner\MrCronRunner;
use Phore\Core\Helper\PhoreGetOptResult;
use Phore\Log\Logger\PhoreSyslogLoggerDriver;
use Phore\Log\PhoreLogger;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class MrCronCli
{


    private function _printHelp()
    {
        echo <<<EOT
Infracamp.org's Mr.Cron

Usage:
    mrcron [options]

Options:
    -h      Show help
    -s      Single Shot: Run one minute and return


EOT;


    }

    public function run(PhoreGetOptResult $opts)
    {
        $logger = new NullLogger();
        if (CONF_SYSLOG_HOST !== null) {
            $logger = new PhoreLogger(new PhoreSyslogLoggerDriver(CONF_SYSLOG_HOST, LogLevel::NOTICE));
        }
        $runner = new MrCronRunner($logger);

        $scrapeUrls = CONF_SCRAPE_URLS;
        if ($opts->has("s")) {
            $scrapeUrls = $opts->get("s");
        }
        $runner->run(explode(";", $scrapeUrls), $opts->has("s"));
    }


    public function main(array $argv, int $argc)
    {
        $opts = phore_getopt("hs:");
        if ($opts->has("h")) {
            $this->_printHelp();
            exit(1);
        }

        $this->run($opts);
    }

}
