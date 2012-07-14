{
	"template": "hoborg",
	"publicPrefix": "",
	"widgets": [
		{
			"name": "Welcome to demo Dashboard.",
			"static": "hoborg/intro.html",
			"size": "span16"
		},
		{
			"name": "Jenkins Job Status",
			"footer": "hoborg/jenkins/widget.php",
			"size": "span8 height2",
			"body" : "Loading Content...",
			"php": "hoborg/jenkins/widget.php",
			"conf" : {
				"data" : "hoborg/jobs.json"
			}
		},
		{
			"name": "Page visits (monthly)",
			"footer": "rate meter widget",
			"size": "span4",
			"body" : "test",
			"php": "hoborg/rate-meter/widget.php",
			"conf" : {
				"data" : "hoborg/numbers-01.json"
			}
		},
		{
			"name": "More ...",
			"static": "hoborg/more.html",
			"size": "span12"
		},
		{
			"name": "www.my-website.com",
			"php": "hoborg/info/widget.php",
			"size": "span6",
			"data": {
				"conf": {
					"file": "hoborg/info-01.json"
				}
			}
		},
		{
			"name": "www.my-second-website.com",
			"php": "hoborg/info/widget.php",
			"size": "span6",
			"data": {
				"conf": {
					"file": "hoborg/info-02.json"
				}
			}
		},
		{
			"name": "api.my-website.com/1",
			"php": "hoborg/info/widget.php",
			"size": "span6",
			"data": {
				"conf": {
					"file": "hoborg/info-03.json"
				}
			}
		},
		{
			"name": "api.my-website.com/1",
			"php": "hoborg/info/widget.php",
			"size": "span6",
			"data": {
				"conf": {
					"file": "hoborg/info-04.json"
				}
			}
		}
	]
}
