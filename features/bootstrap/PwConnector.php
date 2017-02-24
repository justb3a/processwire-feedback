<?php

/**
 * Class PwConnector
 *
 * @author Tabea David
 */
final class PwConnector {

  const USER_PAGE_ID = '29';
  const ROLE_PAGE_ID = '30';

  public $moduleServiceURL;
  public $moduleServiceKey;
  protected $userContainer;
  protected $roleContainer;

  /**
   * Check for ProcessWire
   */
  public function checkForProcessWire() {
    if (!getcwd()) return false;

    if (!is_dir(getcwd() . '/wire')) {
      foreach (new \DirectoryIterator(getcwd()) as $fileInfo) {
        if (is_dir($fileInfo->getPathname() . '/wire')) chdir($fileInfo->getPathname());
      }

      if (!is_dir(getcwd() . '/wire')) {
        chdir('..');

        if (empty(pathinfo(getcwd())['basename'])) {
          return false;
        } else {
          $this->checkForProcessWire();
        }
      }
    }

    return true;
  }

  /**
   * Bootstrap ProcessWire
   */
  public function bootstrapProcessWire() {
    if (!$this->checkForProcessWire()) return false;

    if (!function_exists('\ProcessWire\wire')) include(getcwd() . '/index.php');

    $this->userContainer = \ProcessWire\wire('pages')->get(self::USER_PAGE_ID);
    $this->roleContainer = \ProcessWire\wire('pages')->get(self::ROLE_PAGE_ID);

    $this->moduleServiceURL = \ProcessWire\wire('config')->moduleServiceURL;
    $this->moduleServiceKey = \ProcessWire\wire('config')->moduleServiceKey;
  }

}
