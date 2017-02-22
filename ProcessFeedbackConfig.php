<?php namespace ProcessWire;

/**
 * Class ProcessFeedbackConfig
 */
class ProcessFeedbackConfig extends ModuleConfig {

  /**
   * array Default config values
   */
  public function getDefaults() {
    return array(
      'limit' => 20
    );
  }

  /**
   * Retrieves the list of config input fields
   * Implementation of the ConfigurableModule interface
   *
   * @return InputfieldWrapper
   */
  public function getInputfields() {
    // get inputfields
    $inputfields = parent::getInputfields();

    // field limit
    $field = $this->modules->get('InputfieldInteger');
    $field->name = 'limit';
    $field->label = __('Number of entries per page');
    $field->description = __('The amount of entries per page, defaults to 20.');
    $field->columnWidth = 50;
    $field->required = 1;
    $inputfields->add($field);

    return $inputfields;
  }

}
