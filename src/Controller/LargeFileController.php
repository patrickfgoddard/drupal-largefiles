<?php
namespace Drupal\largefiles\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class LargeFilesController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function listPage() {
    return [
      '#markup' => 'Hello, world',
    ];
  }

}