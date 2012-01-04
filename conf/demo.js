{
  "template": "hoborg",
  "publicPrefix": "",
  "widgets": [
	{
		"name": "Page visits (monthly)",
		"footer": "hoborg/rate-meter/widget.php",
		"size": "span4",
		"body" : "test",
		"php": "hoborg/rate-meter/widget.php",
		"conf" : {
			"data" : "hoborg/numbers-01.json"
		}
	},
	{
		"name": "Code commiters",
		"footer": "hoborg/commiters/widget.php",
		"size": "span8 autoHeight",
		"body" : "Loading Content",
		"php": "hoborg/commiters/widget.php",
		"conf" : {
			"data" : "hoborg/commiters.json",
			"cache" : "cache",
			"mustachify" : 1
		}
	},
	{
		"name": "Jenkins Job Status",
		"footer": "hoborg/jenkins/widget.php",
		"size": "span8 autoHeight",
		"body" : "Loading Content...",
		"php": "hoborg/jenkins/widget.php",
		"conf" : {
			"data" : "hoborg/jobs.json"
		}
	},
	{
		"name": "Issues",
		"footer": "Last update: 23 Oct 2011",
		"size": "span4",
		"static": "demo/issues.html"
	},
	{
		"name": "Chart",
		"footer": "Last update: 23 Oct 2011",
		"size": "span4",
		"php": "demo/chart.html"
	}
  ]
}
