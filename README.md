# Simple Dashboard

Simple Dashboard is a mini-project written to bring into live a crazy vision of
*Phobjects*. Idea born on a PHPNW 2011 conference - or to be more precise, 
on Saturday night, after few of pints of free beer.

- - -

A *Phobject* is a native PHP array with ... lambda functions

- - -


## How, What, Where?

SimpleDashboard is really *SIMPLE*. Each dashboard is based on JSON config 
file. It contain only a template name and list of widgets. The simpliest 
configuration can look like tihs:

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

Because each widget is well defined, it's easy to refreash selected widgets 
based on "reload" value. So if you don't want to write JavaScript to reload 
your widget you can use build in functionality.

- - -

## Widget documentation

### body

Widget body is kept in `body`. It can be a string (or anything that can be 
casted to string) or a function, in which case it's return value will become
new body.

Body can be automatically loaded form other sources if you specify `url`,
`static` or `cgi` parameter.


### url

When specifying `url` by default widget json object will be send as a GET
parameter. You can overwirte it using `_method` parameter (GET, POST).

Content retrived from given url will become new `body`.

### cgi

CGI field is very similar to url. Main difference is that cgi script *MUST*
return json widget object. Again you can specify `_method` or use default GET.

### static

You can include static files as well. Path to static files *MUST* be relative
to /widgets folder. Content of that static file will become new widget body.

### head

You can inject your own html into head section. There are few ways for 
injecting it.

* Once Only
* Always
* On Load

#### Once Only

Once Only option allows you to include some head section only once for given 
type. If two or more widgets shares the same javascript, you can simply include
it like that:

    {
      "name": "my widget",
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
      "name": "my widget",
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
