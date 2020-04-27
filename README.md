# Mr. Cron Cloud Cron Scheduler

Cloud ready cron scheduler service. Mr. Cron will scrape multiple urls for cronjob data and
execute (call desired urls). Process locking is handled by redis server.

## Installation

Just run the publicly available docker image:

```
docker run -e "CONF_SCRAPE_URLS=http://xy/mrcron.json" infracamp/mrcron
```

- How to run MrCron on docker stack
- How to run MrCron on kubernetes


## Configuration

### Environment

**CONF_SCRAPE_URLS**

The urls to scrape for jobdef.json content.

### MrCron jobdef.yaml

```yaml
schedule:
- cron: "<cron timing def>"
  id: <uniqueJobId>
  steps:
    - url: "http://../uri/to/call"
      timeout: 30
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
