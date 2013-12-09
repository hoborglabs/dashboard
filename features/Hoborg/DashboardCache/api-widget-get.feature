Feature: API Widget access



Scenario: Get non-existing widget

  Given clear cache
  When I send a GET request on "/api/1/widget/test:widget"
  Then I should get 404 response
  And the response should be in JSON
  And the JSON node "data" should be equal to ""



Scenario: Get widget with access key

  Given there is a widget
  | id          | config            |
  | test:widget | {"key": "abc123"} |
  # config is encoded JSON string
  When I send a GET request on "/api/1/widget/test:widget?config=%7B%22key%22:%22abc123%22%7D"
  Then I should get 200 response
  And the response should be in JSON
  And the JSON node "id" should be equal to "test:widget"



Scenario: Get widget without passing api key

  Given there is a widget
  | id          | config            |
  | test:widget | {"key": "abc123"} |
  When I send a GET request on "/api/1/widget/test:widget"
  Then I should get 404 response
  And the response should be in JSON



Scenario: Get widget using incorrect api key

  Given there is a widget
  | id          | config            |
  | test:widget | {"key": "abc123"} |
  When I send a GET request on "/api/1/widget/test:widget?config=%7B%22key%22:%22def456%22%7D"
  Then I should get 404 response
  And the response should be in JSON



Scenario: Get widget data

  Given there is a widget
  | id          | data                    |
  | test:widget | {"a": "b", "c": ["d", "e"]} |
  When I send a GET request on "/api/1/widget/test:widget"
  Then I should get 200 response
  And the JSON node "data.a" should be equal to "b"
  And the JSON node "data.c[0]" should be equal to "d"
  And the JSON node "data.c[1]" should be equal to "e"



Scenario: Get widget with data without passing API key

  Given clear cache
  And there is a widget
  | id          | config            | data                    |
  | test:widget | {"key": "abc123"} | {a: "b", c: ["d", "e"]} |
  When I send a GET request on "/api/1/widget/test:widget"
  Then I should get 404 response
  And the response should be in JSON
