{
  "template": "demo",
  "widgets": [
    {
      "size": "message",
      "name": "Last Commit Message",
      "php": "demo/commits.php",
      "conf": {
        "dataFile": "github-commits-hoborglabs-Dashboard.js"
      }
    },
    {
      "name": "Issues",
      "footer": "Last update: 23 Oct 2011",
      "static": "demo/issues.html"
    }, 
    {"name": "test2", "body" : "test"},
    {},
    {
      "name": "test5",
      "footer": "Demo chart widget",
      "size": "full",
      "php": "demo/chart.php"
    }
  ]
}
