# Simple Dashboard - what is it?

It is really simple dashboard which allows you to display widgets from
your local server and from external endpoints - it's all based on JSON.

You can write your widgets in PHP, or in any other language. You just
have to expose http interface which accepts GET or POST request and
returns JSON widget object.

Right now we have widgets for:
* Jenkins jobs statuses
* Graphite graphs
* Git/Github top N commiters
* XenServer VM statuses


## More on Dashboard Home Page

Visit http://dashboard.hoborglabs.com/ for more details.

For more technical info visit: http://dashboard.hoborglabs.com/doc

## Dashboard Cache

Cache:
timestamp, widget id, json

widget:
id, name, api_key

example api call

~~~~~
PUT /api/1/widget/1/data?key=WIDGET_SECREAT_KEY
{"my": "example data"}
~~~~~

## History

Simple Dashboard was started to bring into live a crazy vision of 
*Phobjects*. Idea born on a PHPNW 2011 conference - or to be more 
precise, on Saturday night, after few pints of free beer.


- - -

* A *Phobject* is a native PHP array with ... lambda functions - how 
  crazy is that :) ?
* If you are using our HoborgLabs Dashboard - let me know on wojtek at
  hoborglabs.com
