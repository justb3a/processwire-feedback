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
