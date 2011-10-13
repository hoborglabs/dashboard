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
display external or internal files, and you can even call CGI scripts as long
as it returns json widget object.

The beauty of SimpleDashboard is in its simplicity. There are only THREE 
reserved keys (1) "body", (2) "head" - you will find more about "head" in next 
section - and (3) "reload". Widget body is stored in ... "body". But wait ...
what about Phobjects? The answer is simple - you can assign a lambda function 
to "body" and it will be executed to get your widget body!



## What about auto-refresh

Because each widget is well defined, it's easy to refreash selected widgets 
based on "reload" value.


