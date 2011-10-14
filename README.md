# Simple Dashboard - what is it?

Simple Dashboard is a mini-project written to bring into live a crazy vision of
*Phobjects*. Idea born on a PHPNW 2011 conference - or to be more precise, 
on Saturday night, after few of pints of free beer.

- - -

A *Phobject* is a native PHP array with ... lambda functions

- - -


## How, What, Where?

SimpleDashboard is really *SIMPLE*. Each dashboard is based on JSON config 
file. It contain only a template name and list of widgets. The simplest 
configuration can look like this:

    {
      "template": "hoborg",
      "widgets": [
        {
          "body": "this is my first widget"
        }
      ]
    }


You can of course extend widget with php scripts, but not only! You can 
display external or internal static files, and you can even call CGI scripts as
long as it returns json widget object.

The beauty of SimpleDashboard is in its simplicity. There are only THREE 
reserved keys (1) "body", (2) "head" - you will find more about "head" in next 
section - and (3) "reload". Widget body is stored in ... `"body"`. But wait ...
what about *Phobjects*? The answer is simple - you can assign a lambda function
to `"body"` and it will be executed to get your widget body!



## What about auto-refresh

Because each widget is well defined, it's easy to refresh selected widgets 
based on "reload" value. So if you don't want to write JavaScript to reload 
your widget you can use build in functionality.

- - -

# Widget documentation

## Widget Content

Right, time for some technical details. Body of your widget can be generated
in several ways. SimpleDashboard will search for each key listed below (in
following order):

### body

It can be a string (or anything that can be cast to string) or a function, in
which case its return value will become new widget body.

Because it's loaded first, you can use it as a error/default body of your 
widget, and it can look like this:

    {
      "body" : "<span class=\"warn\">We had some technical issues - sorry.</span>"
    }


### static

You can include static files. Path to static files *MUST* be relative to 
`/widgets` folder. Content of that static file will become new widget body.

In some cases you might want to have a static html file with error/default 
content, and it will look like this:

    {
      "body" : "<span class=\"warn\">We had some technical issues - sorry.</span>",
      "static" : "error.html"
    }


### url

When specifying `url`, SimpleDashboard will call that URL and by default 
widget's json object will be send as a GET parameter `widget`. You can override
httpd method using `_method` parameter (GET, POST).

Content retrieved from given url will become new `body`.

    {
      "body" : "<span class=\"warn\">We had some technical issues - sorry.</span>",
      "static" : "error.html",
      "url" : "http://localhost/my-widget.php"
    }


### cgi

CGI field is very similar to url. Main difference is that cgi script *MUST*
return json widget object. Again you can specify `_method` or use default GET.

    {
      "body" : "<span class=\"warn\">We had some technical issues - sorry.</span>",
      "static" : "error.html",
      "cgi" : "http://localhost/my-widget.cgi"
    }

If CGI address does not begin with `https?`, SimpleDashboard will assume that
you want to execute a local file. In that case your script will get one parameter - the
widget's json object.

    {
      "body" : "<span class=\"warn\">We had some technical issues - sorry.</span>",
      "static" : "error.html",
      "cgi" : "/var/www/dashboard/widgets-cgi/my-scrip"
    }


### php

PHP field is used to include given PHP file. Path to PHP files *MUST* be 
relative to `/widgets` folder. Your PHP file needs to return `$widget` array.

    {
      "body" : "<span class=\"warn\">We had some technical issues - sorry.</span>",
      "static" : "error.html",
      "php" : "my-widget.php"
    }

And your PHP script can look like this:

    <?php
    $widget['body'] = 'my body';
    return $widget;



## How to Include Custom Scripts and Styles?

You can inject your own html into head section. There are few ways for 
injecting it.

* Once Only
* Always
* On Load


#### Once Only

Once Only option allows you to include some head section only once for given 
type. If two or more widgets shares the same JavaScript or CSS files, you can 
simply include it like that:

    {
      "body": "body of my widget",
      "head": {
        "onceOnly": {
          "myWidget": "<script>var test = 'test';</script>"
        }
      }
    }

Of course you don't have to specify the head section in configuration file, you
can set it in your PHP file or via CGI call.


#### On Load

If you don't want to use jQuery, MooTools or any other JS framework, and you 
still want to run some JS on page load, you can simply use `onLoad` section.
All `onLoad` code will be put in one `window.onload` function.

    {
      "body": "body of my widget",
      "head": {
        "onLoad": {
          "myWidget": "var test = 'test';"
        }
      }
    }

With that config you will get this section in head

    <head>
      <script type="text/javascript">
        window.onload = function () {
          var test = 'test';
        }
      </script>
    </head>
