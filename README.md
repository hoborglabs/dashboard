# Simple Dashboard - what is it?

[Develop ![Develop Status](https://api.travis-ci.org/hoborglabs/dashboard.svg?branch=develop)](https://travis-ci.org/hoborglabs/dashboard)

[Master ![Master Status](https://api.travis-ci.org/hoborglabs/dashboard.svg?branch=master)](https://travis-ci.org/hoborglabs/dashboard)

It is really simple dashboard which allows you to display widgets from your local server and from external endpoints -
it's all based on JSON.

You can write your widgets in PHP, or in any other language. You just have to expose http interface which accepts GET or
POST request and returns JSON widget object.

Right now we have widgets for:

* Jenkins/Hudson jobs statuses
* Graphite graphs
* Git/Github top N committers
* XenServer VM statuses




## More on Dashboard Home Page

Visit http://dashboard.hoborglabs.com/ for more details.

For more technical info visit: http://dashboard.hoborglabs.com/doc




## For Developers

You need `ant` if, like me, you are a bit lazy. Then all you need is `ant validate.dev test` to get all dev
dependencies and run unit tests.

To run dashboard locally for development purposes, simply do

```BASH
cd example/htdocs
DASHBOARD_ROOT=`pwd`/.. php -S localhost:8081
```

To run tests, simply run `ant test`




## Dashboard Cache

Dashboard Cache is a small storage application that allows you to store
your widget data. It's particularly useful when your want to store data
from remote servers. You can, for instance, run a simple cron jab to
send 10min average cpu/disk usage.

Project itself is small enough to be a part of Dashbaord project, there
is however separate Kernel class for handling DashboardCache requests.


Cache:
timestamp, widget id, numeric, json

widget:
id, name, api_key

example api call

~~~~~
PUT /api/1/widget/1/data?key=WIDGET_SECREAT_KEY
GET /api/1/widget/1/data?key=WIDGET_SECREAT_KEY
GET /api/1/widget/1/data?from=-10min&to=now&key=WIDGET_SECREAT_KEY
~~~~~

time format: `-?\d+(min|h|d|w|m|y)`
  accept more complicated formats like `midnight-1d`, `today`


Data split:

Hot data - in memory  
Warm data - normal storage  
Cold data - archived




- - -

If you are using HoborgLabs Dashboard - let me know on wojtek at hoborglabs.com
