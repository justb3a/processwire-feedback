Feature: RestFul Api Testing with Behat

 As an user
 I want to send feedback
 So that I'll get the help that I need

@send @success
Scenario: Send feedback
  Given I send feedback via ProcessWire
  Then the response should be JSON
  And the response has a "success" property
  And the "success" property equals boolean "true"

@send @error
Scenario: Send feedback using incorrect credentials
  Given I send feedback including key
  Then the response should be JSON
  And the response has a "success" property
  And the "success" property equals boolean "false"
  And the response has a "error" property
  And the "error" property equals "incorrect_client_credentials"
