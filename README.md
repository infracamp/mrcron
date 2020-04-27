# Mr. Cron Cloud Cron Scheduler

Cloud ready cron scheduler service. Mr. Cron will scrape multiple urls for cronjob data and
execute (call desired urls). No additional services are needed.

![MrCron Command flow](docs/mrcron-1.png)

Features:
- No database / persistence needed
- No complex configuration
- Handles up to 500 Cron Requests in parallel
- Services define their own jobs (secret job-tokens can be applied)
- UDP Syslog aware logging interface
- Compatible to wildly used [crontab](https://en.wikipedia.org/wiki/Cron) format 

## Installation / Running MrCron

Just run the publicly available docker image from [DockerHub](https://hub.docker.com/r/infracamp/mrcron):

```
docker run -e "CONF_SCRAPE_URLS=http://xy/mrcron.json" infracamp/mrcron
```

- **[Demo docker-compose.yml](docs/docker-stack-mrcron.yml)**: Compose file
  to deploy MrCron directly to docker-swarm stack.
- **[Demo kubernetes service.yml]()**: Deploy on kubernetes
- **[Demo mrcron-jobs.json](docs/demo-mrcron-jobs.json)**: This file can
  be added to every service that has cronjobs to run

## Configuration

### Environment

**CONF_SCRAPE_URLS**

The urls to scrape for jobdef.json content. Multiple URLs can be specified
separated by semicolon.

### MrCron jobdef.json

```json
{
  "jobs": [
    {
      "id": "Unique job identifier",
      "cron": "<cron timing def>",
      "urls": [
        "http://some.tld/to/call"  
      ]   
    },
    ..next job..
  ]
}
```

### Cron timing def

```
* * * * *
┬ ┬ ┬ ┬ ┬
│ │ │ │ │
│ │ │ │ └──── Weekday (0-7, Sunday is 0)
│ │ │ └────── Month (1-12)
│ │ └──────── Day (1-31)
│ └────────── Hour (0-23)
└──────────── Minute (0-59)
```

**Examples**

```* * * * *```: Run the job each minute

```0 * * * *```: Will run the job once per hour at xx:00

```0 0 * * *```: Run the job daily at Midnight (00:00)

```0 0 1 * *```: Run the job on Midnight (00:00) on first day of the month

```0 0 * * 0```: Run the job on Midnight (00:00) on each Sunday (0 => Sunday)
