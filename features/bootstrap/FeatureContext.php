<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use PhilipBrown\Signature\Auth;
use PhilipBrown\Signature\Token;
use PhilipBrown\Signature\Request;
use PhilipBrown\Signature\Guards\CheckKey;
use PhilipBrown\Signature\Guards\CheckVersion;
use PhilipBrown\Signature\Guards\CheckTimestamp;
use PhilipBrown\Signature\Guards\CheckSignature;
use PhilipBrown\Signature\Exceptions\SignatureException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context {
  public $_response;
  private $_params;

  /**
    * Initializes context.
    *
    * Every scenario gets its own context instance.
    * You can also pass arbitrary arguments to the
    * context constructor through behat.yml.
    *
    * @param string $baseUrl
    * @param string $key
    * @param string $secret
    * @param array $data
    */
  public function __construct($baseUrl, $key, $secret, $data) {
    $this->_params = array(
      'baseurl' => $baseUrl,
      'key' => $key,
      'secret' => $secret,
      'data' => $data
    );
  }

  /**
   * @When /^I request "([^"]*)"$/
   */
  public function iRequest($uri) {
    $this->_params['endpoint'] = $this->_params['baseurl'] . $uri;
  }

  /**
  * @Given I pass all data correctly
  */
  public function iPassAllDataCorrectly() {
    $token = new Token($this->_params['key'], $this->_params['secret']);
    $request = new Request('POST', $this->_params['endpoint'], $this->_params['data']);
    $authParams = $request->sign($token);
    $queryParams = array_merge($authParams, $this->_params['data']);

    $client = new Client(array(
      'headers'  => ['content-type' => 'application/json'],
      'body' => json_encode($queryParams)
    ));

    $this->_response = $client->request('POST', $this->_params['endpoint']);
  }

  /**
   * @Given I pass incorrect content-type :type
   */
  public function iPassIncorrectContentType($type) {
    $token = new Token($this->_params['key'], $this->_params['secret']);
    $request = new Request('POST', $this->_params['endpoint'], $this->_params['data']);
    $authParams = $request->sign($token);
    $queryParams = array_merge($authParams, $this->_params['data']);

    $client = new Client(array(
      'headers'  => ['content-type' => $type],
      'body' => json_encode($queryParams),
      'http_errors' => false
    ));

    $this->_response = $client->request('POST', $this->_params['endpoint']);
  }

  /**
   * @Given I pass incorrect request method :method
   */
  public function iPassIncorrectRequestMethod($method) {
    $token = new Token($this->_params['key'], $this->_params['secret']);
    $request = new Request($method, $this->_params['endpoint'], $this->_params['data']);
    $authParams = $request->sign($token);
    $queryParams = array_merge($authParams, $this->_params['data']);

    $client = new Client(array(
      'headers'  => ['content-type' => 'application/json'],
      'body' => json_encode($queryParams),
      'http_errors' => false
    ));

    $this->_response = $client->request($method, $this->_params['endpoint']);
  }

  /**
   * @Given I pass incorrect client credentials
   */
  public function iPassIncorrectClientCredentials() {
    $token = new Token('incorrectKey', 'incorrectSecret');
    $request = new Request('POST', $this->_params['endpoint'], $this->_params['data']);
    $authParams = $request->sign($token);
    $queryParams = array_merge($authParams, $this->_params['data']);

    $client = new Client(array(
      'headers'  => ['content-type' => 'application/json'],
      'body' => json_encode($queryParams),
      'http_errors' => false
    ));

    $this->_response = $client->request('POST', $this->_params['endpoint']);
  }

  /**
   * @Given I do not pass any parameters
   */
  public function iDoNotPassAnyParameters() {
    $token = new Token($this->_params['key'], $this->_params['secret']);
    $request = new Request('POST', $this->_params['endpoint'], $this->_params['data']);
    $authParams = $request->sign($token);
    $queryParams = array_merge($authParams, $this->_params['data']);

    $client = new Client(array(
      'headers'  => ['content-type' => 'application/json'],
      'http_errors' => false
    ));

    $this->_response = $client->request('POST', $this->_params['endpoint']);
  }

  /**
   * @Then /^the response should be JSON$/
   */
  public function theResponseShouldBeJson() {
    $data = json_decode($this->_response->getBody(true));
    if (empty($data)) {
      throw new Exception("Response was not JSON\n" . $this->_response);
    }
  }

  /**
   * @Then /^the response status code should be (\d+)$/
   */
  public function theResponseStatusCodeShouldBe($httpStatus) {
    if ((string)$this->_response->getStatusCode() !== $httpStatus) {
      throw new \Exception('HTTP code does not match '. $httpStatus .
      ' (actual: '.$this->_response->getStatusCode().')');
    }
  }

  /**
   * @Given /^the response has a "([^"]*)" property$/
   */
  public function theResponseHasAProperty($propertyName) {
    $data = json_decode($this->_response->getBody(true));
    if (!empty($data)) {
      if (!isset($data->$propertyName)) {
        throw new Exception("Property '".$propertyName."' is not set!\n");
      }
    } else {
      throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
    }
  }

  /**
   * @Then the :propertyName property equals boolean :propertyValue
   */
  public function thePropertyEqualsBoolean($propertyName, $propertyValue) {
    $propertyValue = $propertyValue === 'true' ? true : false;
    $this->thePropertyEquals($propertyName, $propertyValue);
  }

  /**
   * @Then /^the "([^"]*)" property equals "([^"]*)"$/
   */
  public function thePropertyEquals($propertyName, $propertyValue) {
    $data = json_decode($this->_response->getBody(true));

    if (!empty($data)) {
      if (!isset($data->$propertyName)) {
        throw new Exception("Property '".$propertyName."' is not set!\n");
      }

      if ($data->$propertyName !== $propertyValue) {
        throw new \Exception('Property value mismatch! (given: '.$propertyValue.', match: '.$data->$propertyName.')');
      }
    } else {
      throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
    }
  }
}
