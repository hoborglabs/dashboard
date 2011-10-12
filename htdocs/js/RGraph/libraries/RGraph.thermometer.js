    /**
    * o------------------------------------------------------------------------------o
    * | This file is part of the RGraph package - you can learn more at:             |
    * |                                                                              |
    * |                          http://www.rgraph.net                               |
    * |                                                                              |
    * | This package is licensed under the RGraph license. For all kinds of business |
    * | purposes there is a small one-time licensing fee to pay and for non          |
    * | commercial  purposes it is free to use. You can read the full license here:  |
    * |                                                                              |
    * |                      http://www.rgraph.net/LICENSE.txt                       |
    * o------------------------------------------------------------------------------o
    */

    if (typeof(RGraph) == 'undefined') RGraph = {};

    /**
    * The chart constructor. This function sets up the object. It takes the ID (the HTML attribute) of the canvas as the
    * first argument and the data as the second. If you need to change this, you can.
    * 
    * @param string id    The canvas tag ID
    * @param number min   The minimum value
    * @param number max   The maximum value
    * @param number value The value reported by the thermometer
    */
    RGraph.Thermometer = function (id, min, max, value)
    {
        this.id      = id;
        this.canvas  = document.getElementById(id);
        this.context = this.canvas.getContext ? this.canvas.getContext("2d") : null;
        this.canvas.__object__ = this;

        this.type      = 'thermometer';
        this.isRGraph  = true;
        this.min       = min;
        this.max       = max;
        this.value     = value;
        this.coords    = [];
        this.graphArea = [];

        RGraph.OldBrowserCompat(this.context);

        this.properties = {
            'chart.width':                  null,
            'chart.height':                 null,
            'chart.colors':                 ['red'],
            'chart.gutter':                 15,
            'chart.ticksize':               5,
            'chart.text.font':              'Verdana',
            'chart.text.size':              10,
            'chart.units.pre':              '',
            'chart.units.post':             '',
            'chart.zoom.factor':            1.5,
            'chart.zoom.fade.in':           true,
            'chart.zoom.fade.out':          true,
            'chart.zoom.hdir':              'right',
            'chart.zoom.vdir':              'down',
            'chart.zoom.frames':            10,
            'chart.zoom.delay':             50,
            'chart.zoom.shadow':            true,
            'chart.zoom.mode':              'canvas',
            'chart.zoom.thumbnail.width':   75,
            'chart.zoom.thumbnail.height':  75,
            'chart.zoom.background':        true,
            'chart.title':                  '',
            'chart.title.side':             '',
            'chart.shadow':                 true,
            'chart.shadow.offsetx':         0,
            'chart.shadow.offsety':         0,
            'chart.shadow.blur':            15,
            'chart.shadow.color':           'gray',
            'chart.resizable':              false,
            'chart.contextmenu':            null
        }

        /**
        * A simple check that the browser has canvas support
        */
        if (!this.canvas) {
            alert('[THERMOMETER] No canvas support');
            return;
        }
    }




    /**
    * A setter.
    * 
    * @param name  string The name of the property to set
    * @param value mixed  The value of the property
    */
    RGraph.Thermometer.prototype.Set = function (name, value)
    {
        this.properties[name.toLowerCase()] = value;
    }




    /**
    * A getter.
    * 
    * @param name  string The name of the property to get
    */
    RGraph.Thermometer.prototype.Get = function (name)
    {
        return this.properties[name];
    }




    /**
    * Draws the thermometer
    */
    RGraph.Thermometer.prototype.Draw = function ()
    {
        /**
        * Fire the custom RGraph onbeforedraw event (which should be fired before the chart is drawn)
        */
        RGraph.FireCustomEvent(this, 'onbeforedraw');

        /**
        * Clear all of this canvases event handlers (the ones installed by RGraph)
        */
        RGraph.ClearEventListeners(this.id);



        /**
        * Draw the background
        */
        this.DrawBackground();
        
        /**
        * Draw the bar that represents the value
        */
        this.DrawBar();

        /**
        * Draw the tickmarks/hatchmarks
        */
        this.DrawTickMarks();

        /**
        * Draw the label
        */
        this.DrawLabels();

        /**
        * Draw the title
        */
        if (this.Get('chart.title')) {
            this.DrawTitle();
        }
        
        /**
        * Draw the side title
        */
        if (this.Get('chart.title.side')) {
            this.DrawSideTitle();
        }
        
        /**
        * This function enables resizing
        */
        if (this.Get('chart.resizable')) {
            RGraph.AllowResizing(this);
        }
        
        
        /**
        * Setup the context menu if required
        */
        if (this.Get('chart.contextmenu')) {
            RGraph.ShowContext(this);
        }

        /**
        * If the canvas is annotatable, do install the event handlers
        */
        if (this.Get('chart.annotatable')) {
            RGraph.Annotate(this);
        }



        
        /**
        * Fire the custom RGraph ondraw event (which should be fired when you have drawn the chart)
        */
        RGraph.FireCustomEvent(this, 'ondraw');
    }





    /**
    * Draws the thermometer itself
    */
    RGraph.Thermometer.prototype.DrawBackground = function ()
    {
        var gutter     = this.Get('chart.gutter');
        var canvas     = this.canvas;
        var context    = this.context;
        var bulbRadius = (RGraph.GetWidth(this) - (2 * this.Get('chart.gutter'))) / 2;

        // Draw the black background that becomes the border
        context.beginPath();
            context.fillStyle = 'black';

            if (this.Get('chart.shadow')) {
                RGraph.SetShadow(this, this.Get('chart.shadow.color'), this.Get('chart.shadow.offsetx'), this.Get('chart.shadow.offsety'), this.Get('chart.shadow.blur'));
            }

            context.fillRect(gutter + 12,gutter + bulbRadius,RGraph.GetWidth(this) - gutter - gutter - 24, RGraph.GetHeight(this) - gutter - gutter - bulbRadius - bulbRadius);
            context.arc(RGraph.GetWidth(this) / 2, RGraph.GetHeight(this) - gutter - bulbRadius, bulbRadius, 0, 6.28, 0);
            context.arc(RGraph.GetWidth(this) / 2,gutter + bulbRadius,(RGraph.GetWidth(this) - gutter - gutter - 24)/ 2,0,6.28,0);
        context.fill();
        
        RGraph.NoShadow(this);

        // Draw the white inner content background that creates the border
        context.beginPath();
            context.fillStyle = 'white';
            context.fillRect(gutter + 12 + 1,gutter + bulbRadius,RGraph.GetWidth(this) - gutter - gutter - 24 - 2,RGraph.GetHeight(this) - gutter - gutter - bulbRadius - bulbRadius);
            context.arc(RGraph.GetWidth(this) / 2, RGraph.GetHeight(this) - gutter - bulbRadius, bulbRadius - 1, 0, 6.28, 0);
            context.arc(RGraph.GetWidth(this) / 2,gutter + bulbRadius,((RGraph.GetWidth(this) - gutter - gutter - 24)/ 2) - 1,0,6.28,0);
        context.fill();
        
        // Draw the bottom content of the thermometer
        context.beginPath();
            context.fillStyle = this.Get('chart.colors')[0];
            context.arc(RGraph.GetWidth(this) / 2, RGraph.GetHeight(this) - gutter - bulbRadius, bulbRadius - 1, 0, 6.28, 0);
            context.fillRect(gutter + 12 + 1, RGraph.GetHeight(this) - gutter - bulbRadius - bulbRadius,RGraph.GetWidth(this) - gutter - gutter - 24 - 2, bulbRadius);
        context.fill();
        
        // Save the X/Y/width/height
        this.graphArea[0] = gutter + 12 + 1;
        this.graphArea[1] = gutter + bulbRadius;
        this.graphArea[2] = RGraph.GetWidth(this) - gutter - gutter - 24 - 2;
        this.graphArea[3] = (RGraph.GetHeight(this) - gutter - bulbRadius - bulbRadius) - (this.graphArea[1]);
    }


    /**
    * This draws the bar that indicates the value of the thermometer
    */
    RGraph.Thermometer.prototype.DrawBar = function ()
    {
        var barHeight = (this.value / (this.max - this.min)) * this.graphArea[3];
        var context   = this.context;

        // Draw the actual bar that indicates the value
        context.beginPath();
            context.fillStyle = this.Get('chart.colors')[0];
            context.fillRect(this.graphArea[0],this.graphArea[1] + this.graphArea[3] - barHeight,this.graphArea[2],barHeight);
        context.fill();
        
        this.coords = [this.graphArea[0],this.graphArea[1] + this.graphArea[3] - barHeight,this.graphArea[2],barHeight];
    }

    
    /**
    * Draws the tickmarks of the thermometer
    */
    RGraph.Thermometer.prototype.DrawTickMarks = function ()
    {
        var ticksize = this.Get('chart.ticksize');

        // Left hand side tickmarks
        for (var i=this.graphArea[1]; i<=(this.graphArea[1] + this.graphArea[3]); i += (this.graphArea[3] / 10)) {
            this.context.beginPath();
                this.context.moveTo(this.Get('chart.gutter') + 12, i);
                this.context.lineTo(this.Get('chart.gutter') + 12 + ticksize, i);
            this.context.stroke();
        }

        // Right hand side tickmarks
        for (var i=this.graphArea[1]; i<=(this.graphArea[1] + this.graphArea[3]); i += (this.graphArea[3] / 10)) {
            this.context.beginPath();
                this.context.moveTo(RGraph.GetWidth(this) - (this.Get('chart.gutter') + 12), i);
                this.context.lineTo(RGraph.GetWidth(this) - (this.Get('chart.gutter') + 12 + ticksize), i);
            this.context.stroke();
        }
    }

    
    /**
    * Draws the labels (well, theres only one) of the thermometer
    */
    RGraph.Thermometer.prototype.DrawLabels = function ()
    {
        this.context.beginPath();
            this.context.fillStyle = 'black';
            RGraph.Text(this.context,this.Get('chart.text.font'),this.Get('chart.text.size'),RGraph.GetWidth(this) / 2,this.coords[1] + this.Get('chart.text.size'),this.Get('chart.units.pre') + String(this.value) + this.Get('chart.units.post'),'center','center',true,null,'white');
        this.context.fill();
    }

    
    /**
    * Draws the title
    */
    RGraph.Thermometer.prototype.DrawTitle = function ()
    {
        this.context.beginPath();
            this.context.fillStyle = 'black';
            RGraph.Text(this.context,
                        this.Get('chart.text.font'),
                        this.Get('chart.text.size') + 2,
                        RGraph.GetWidth(this) / 2,
                        this.Get('chart.gutter') / 2,
                        String(this.Get('chart.title')),
                        'center',
                        'center',null,null,null,true);
        this.context.fill();
    }

    
    /**
    * Draws the title
    */
    RGraph.Thermometer.prototype.DrawSideTitle = function ()
    {
        this.context.beginPath();
            this.context.fillStyle = 'black';
            RGraph.Text(this.context,
                        this.Get('chart.text.font'),
                        this.Get('chart.text.size') + 2,
                        this.Get('chart.gutter') / 2,
                        RGraph.GetHeight(this) / 2,
                        String(this.Get('chart.title.side')),
                        'center',
                        'center',null,270,null,true);
        this.context.fill();
    }