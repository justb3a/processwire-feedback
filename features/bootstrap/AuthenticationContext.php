<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
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
class AuthenticationContext implements Context {
  private $_suite;
  public $_response;
  private $_params;
  private $_pwconnector;

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
  public function __construct($url, $key, $secret, $data) {
    $this->_params = array(
      'content-type' => 'application/json',
      'method' => 'POST',
      'endpoint' => $url,
      'key' => $key,
      'secret' => $secret,
      'data' => $data
    );
  }

  /** @BeforeScenario */
  public function gatherContexts(BeforeScenarioScope $scope) {
    $this->_suite = $scope->getSuite()->getName();
  }

  private function send() {
    if ($this->_suite === 'hashed') {
      $this->sendHashed();
    } else {
      $this->sendBasicHttp();
    }
  }

  // hashed
  private function sendHashed() {
    $token = new Token($this->_params['key'], $this->_params['secret']);
    $request = new Request($this->_params['method'], $this->_params['endpoint'], $this->_params['data']);
    $authParams = $request->sign($token);
    $queryParams = $this->_params['data'] ? array_merge($authParams, $this->_params['data']) : array();

    $client = new Client(array(
      'headers'  => ['content-type' => $this->_params['content-type']],
      'body' => json_encode($queryParams),
      'http_errors' => false
    ));

    $this->_response = $client->request($this->_params['method'], $this->_params['endpoint']);
  }


  // basic http auth
  private function sendBasicHttp() {
    $client = new Client(array(
      'headers' => array('content-type' => $this->_params['content-type']),
      'auth' => array($this->_params['key'], $this->_params['secret']),
      'body' => json_encode($this->_params['data']),
      'http_errors' => false
    ));

    $this->_response = $client->request($this->_params['method'], $this->_params['endpoint']);
  }

  /**
  * @Given I pass all data correctly
  */
  public function iPassAllDataCorrectly() {
    $this->send();
  }

  /**
   * @Given I pass incorrect content-type :type
   */
  public function iPassIncorrectContentType($type) {
    $this->_params['content-type'] = $type;
    $this->send();
  }

  /**
   * @Given I pass incorrect request method :method
   */
  public function iPassIncorrectRequestMethod($method) {
    $this->_params['method'] = $method;
    $this->send();
  }

  /**
   * @Given I pass incorrect client credentials
   */
  public function iPassIncorrectClientCredentials() {
    $this->_params['key'] = 'incorrectKey';
    $this->_params['secret'] = 'incorrectSecret';
    $this->send();
  }

  /**
   * @Given I do not pass any parameters
   */
  public function iDoNotPassAnyParameters() {
    $this->_params['data'] = array();
    $this->send();
  }

  /**
   * @Given I pass the property :propertyName with value :propertyValue
   */
  public function iPassThePropertyWithValue($propertyName, $propertyValue) {
    $this->_params['data'] = array_merge($this->_params['data'], array($propertyName => $propertyValue));
    $this->send();
  }

  /**
   * @Then /^the response should be JSON$/
   */
  public function theResponseShouldBeJson() {
    $data = $this->getData();
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

  private function getData() {
    if ($this->_response instanceof GuzzleHttp\Psr7\Response) {
      $data = json_decode($this->_response->getBody(true));
    } else {
      $data = json_decode($this->_response);
    }

    return $data;
  }

  /**
   * @Given /^the response has a "([^"]*)" property$/
   */
  public function theResponseHasAProperty($propertyName) {
    $data = $this->getData();
    if (!empty($data)) {
      if (!isset($data->$propertyName)) {
        throw new Exception("Property '".$propertyName."' is not set!\n");
      }
    } else {
      throw new Exception("Response was not JSON\n" . $data);
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
    $data = $this->getData();

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

  /**
    * @Given I send feedback via ProcessWire
    */
  public function iSendFeedbackViaProcesswire() {
    $this->_pwconnector = new PwConnector();
    $this->_pwconnector->bootstrapProcessWire();

    $this->_response = \ProcessWire\wire('modules')->get('Feedback')->sendFeedback($this->_params['data']);
  }

  /**
   * @Given I send feedback including key
   */
  public function iSendFeedbackIncludingKey() {
    $this->_pwconnector = new PwConnector();
    $this->_pwconnector->bootstrapProcessWire();

    $this->_response = \ProcessWire\wire('modules')->get('Feedback')->sendFeedback($this->_params['data'], 'incorrectKey');
  }

}
