<?php


namespace MrCron;


use MrCron\Runner\MrCronRunner;
use Phore\Log\Logger\PhoreSyslogLoggerDriver;
use Phore\Log\PhoreLogger;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class MrCronCli
{


    private function _printHelp()
    {
        echo "Phore MrCron command scheduler";

    }

    public function run()
    {
        $logger = new NullLogger();
        if (CONF_SYSLOG_HOST !== null) {
            $logger = new PhoreLogger(new PhoreSyslogLoggerDriver(CONF_SYSLOG_HOST, LogLevel::NOTICE));
        }
        $runner = new MrCronRunner($logger);
        $runner->run(explode(";", CONF_SCRAPE_URLS));
    }


    public function main(array $argv, int $argc)
    {
        $opts = phore_getopt("h");
        if ($opts->has("h")) {
            $this->_printHelp();
            exit(1);
        }
        $this->run();
    }

}
