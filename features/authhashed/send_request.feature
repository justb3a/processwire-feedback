Feature: RestFul Api Testing with Behat

 As an user
 I want to send feedback
 So that I'll get the help that I need

Scenario: Send feedback
  Given I send feedback via ProcessWire
  Then the response should be JSON
  And the response has a "success" property
  And the "success" property equals boolean "true"
