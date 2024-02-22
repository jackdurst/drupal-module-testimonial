<?php

/**
 * @file
 * XXX
 */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase {
  public function simpleContent() {
    return[
      '#type' => 'markup',
      '#markup' => t('Hello Drupal world,time flies like an arrow'),
    ];
  }

  public function variableContent($name_1, $name_2) {
    return[
      '#type' => 'markup',
      '#markup' => t('@name1 and @name2 say hello to u !',
        ['@name1' => $name_1, '@name2' => $name_2]),
    ];
  }
}
