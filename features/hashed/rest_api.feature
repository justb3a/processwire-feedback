Feature: RestFul Api Testing with Behat

 As a behat user
 I want to test restful api of this module
 So that it will bring smile for ProcessWire community

Scenario: Successfull api request using key/secret (hashed signature)
  Given I pass all data correctly
  Then the response should be JSON
  And the response status code should be 201
  And the response has a "success" property
  And the "success" property equals boolean "true"

Scenario: Unsuccessfull api request because of incorrect content type
  Given I pass incorrect content-type "text/plain"
  Then the response should be JSON
  And the response status code should be 400
  And the response has a "success" property
  And the "success" property equals boolean "false"
  And the response has a "error" property
  And the "error" property equals "incorrect_content_type"

Scenario: Unsuccessfull api request because of incorrect request method
  Given I pass incorrect request method "GET"
  Then the response should be JSON
  And the response status code should be 400
  And the response has a "success" property
  And the "success" property equals boolean "false"
  And the response has a "error" property
  And the "error" property equals "incorrect_request_method"

Scenario: Unsuccessfull api request because of incorrect authentication
  Given I pass incorrect client credentials
  Then the response should be JSON
  And the response status code should be 401
  And the response has a "success" property
  And the "success" property equals boolean "false"
  And the response has a "error" property
  And the "error" property equals "incorrect_client_credentials"

Scenario: Unsuccessfull api request because of missing body
  Given I do not pass any parameters
  Then the response should be JSON
  And the response status code should be 400
  And the response has a "success" property
  And the "success" property equals boolean "false"
  And the response has a "error" property
  And the "error" property equals "missing_parameters"

Scenario: Unsuccessfull api request because of invalid params
  Given I pass the property "email" with value "invalid"
  Then the response should be JSON
  And the response status code should be 400
  And the response has a "success" property
  And the "success" property equals boolean "false"
  And the response has a "error" property
  And the "error" property equals "validation_error"
