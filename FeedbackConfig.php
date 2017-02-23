<?php namespace ProcessWire;

/**
 * Class FeedbackConfig
 */
class FeedbackConfig extends ModuleConfig {

  /**
   * array Default config values
   */
  public function getDefaults() {
    return array(
      'authentication' => 'sha',
      'apiUser' => '',
      'apiKey' => '',
      'allFields' => array(),
      'addFields' => '',
      'saveMessagesParent' => false,
      'saveMessagesScheme' => ''
    );
  }

  /**
   * Retrieves the list of config input fields
   * Implementation of the ConfigurableModule interface
   *
   * @return InputfieldWrapper
   */
  public function getInputfields() {
    $allFields = isset($this->data['allFields']) ? $this->data['allFields'] : array();

    // get inputfields
    $inputfields = parent::getInputfields();

    // field choose authentication method
    $field = $this->modules->get('InputfieldSelect');
    $field->name = 'authentication';
    $field->label = __('Authentication method');
    $field->description = __('Choose your preferred authentication method, `Hashed signatures` is recommended.');

    $notes = array(
      __('The popular choice is `HTTP Basic` because all you\'ve to do is to pass your username and password.'),
      __('However sending such information across the wire isn\'t the most secure approach.'),
      __('OAuth is another popular choice, but often it\'s an overkill.'),
      __('Sending the request as a hash (using a shared key and secret including a timestamp so the hash will be different every time) is a good alternative.')
    );

    $field->notes = implode(' ', $notes);
    $field->required = 1;
    $field->addOption('sha', 'Key/Secret using hashed signatures');
    $field->addOption('http', 'Basic HTTP authentication');
    $inputfields->add($field);


// HTTP Basic is a popular choice because of how easy it is to use. All you have to do is copy and paste your username and password or API key and you can start interacting with the API straight away.

// However sending your username and password or API key across the wire isn’t the most secure approach (Why the hell does your API still use HTTP Basic Auth?).

// Oauth is another popular choice, but it is probably overkill if all you want to do is to authenticate your application with your API.

// A third option is to use a shared key and secret to hash the request. This means the request is sent as a hash, and can include a timestamp so the hash will be different every time you send it.

    // field apiUser
    $field = $this->modules->get('InputfieldText');
    $field->name = 'apiUser';
    $field->label = __('API username');
    $field->description = __('For basic HTTP Authentification.');
    $field->columnWidth = 50;
    $field->required = 1;
    $inputfields->add($field);

    // field apiKey
    $field = $this->modules->get('InputfieldText');
    $field->name = 'apiKey';
    $field->label = __('API Key');
    $field->description = __('For basic HTTP Authentification.');
    $field->columnWidth = 50;
    $field->required = 1;
    $inputfields->add($field);

    // allFields field
    $field = $this->modules->get('InputfieldAsmSelect');
    $field->description = __('Add all fields (choose from existing ones) which should be attached to the form.');
    $field->addOption('', '');
    $field->label = __('Select form fields');
    $field->attr('name', 'allFields');
    $field->required = true;
    $field->columnWidth = 50;
    foreach ($this->fields as $f) {
      // skip system fields
      if ($f->flags & Field::flagSystem || $f->flags & Field::flagPermanent) continue;
      $field->addOption($f->name, $f->name);
    }
    $field->attr('value', $allFields);
    $inputfields->add($field);

    // field addFields
    $field = $this->modules->get('InputfieldText');
    $field->name = 'addFields';
    $field->label = __('Create and add fields');
    $field->description = __('If you want to add non-existing fields, add them here as a comma-separated list.');
    $field->columnWidth = 50;
    $inputfields->add($field);

    // save messages parent field
    $field = $this->modules->get('InputfieldPageListSelect');
    $field->name = 'saveMessagesParent';
    $field->label = __('Select a parent for items');
    $field->description = __('All items created and managed will live under the parent you select here.');
    $field->required = 1;
    $field->columnWidth = 50;
    $inputfields->add($field);

    // save messages name scheme
    $field = $this->modules->get('InputfieldAsmSelect');
    $field->name = 'saveMessagesScheme';
    $field->description = __('Add all fields which should be used as part of the page name. Choose from existing ones, you may have to add them first below and save.');
    $field->notes = __('The page name starts with a timestamp. All fields added above will be appended.');
    $field->addOption('', '');
    $field->label = __('Select page name fields');
    $field->columnWidth = 50;
    foreach ($allFields as $aField) {
      $f = $this->fields->get($aField);
      $field->addOption($f->name, $f->name);
    }
    $inputfields->add($field);

    return $inputfields;
  }

}
