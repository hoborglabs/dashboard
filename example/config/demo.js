{
	"template": "hoborg",
	"publicPrefix": "",
	"widgets": [
		{
			"name": "Welcome to demo Dashboard 2.",
			"php": "\\Hoborg\\Widget\\Content",
			"size": "grid-1-2 grid-hh-1-1",
			"config": {
				"file": "hoborg/intro.html"
			}
		},
		{
			"name": "More ...",
			"php": "\\Hoborg\\Widget\\Content",
			"size": "grid-1-2 grid-hh-1-1",
			"config": {
				"file": "hoborg/more.html"
			}
		},
		{
			"name": "www.my-website.com test",
			"php": "hoborg/info/rate.php",
			"size": "grid-1-4",
			"data": {
				"conf": {
					"file": "hoborg/info-01.json"
				}
			}
		},
		{
			"name": "www.my-second-website.com",
			"php": "hoborg/info/widget.php",
			"size": "grid-1-4",
			"data": {
				"conf": {
					"file": "hoborg/info-02.json"
				}
			}
		},
		{
			"name": "api.my-website.com/1",
			"php": "hoborg/info/widget.php",
			"size": "grid-1-4",
			"data": {
				"conf": {
					"file": "hoborg/info-03.json"
				}
			}
		},
		{
			"name": "api.my-website.com/1",
			"php": "hoborg/info/widget.php",
			"size": "grid-1-4",
			"data": {
				"conf": {
					"file": "hoborg/info-04.json"
				}
			}
		}
	]
}
