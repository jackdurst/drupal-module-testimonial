<?php

/**
 * @file
 * Creates a block which displays the Testimonial Submit Form.
 */

namespace Drupal\testimonial\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides the TestimonialSubmitForm main Block.
 *
 * @Block(
 *   id = "testimonial_form_block",
 *   admin_label = @Translation("The Testimonial Submit Form Block")
 * )
 */
class TestimonialSubmitFormBlock extends BlockBase {
  /**
   * @inheritdoc
   */
  public function build() {

    return \Drupal::formBuilder()->getForm('Drupal\testimonial\Form\TestimonialSubmitForm');
  }

  /**
   * @inheritdoc
   */
  protected function blockAccess(AccountInterface $account) {
    // Check if the user has permission to access the testimonial form block.
    return AccessResult::allowedIfHasPermission($account, 'access testimonial submit form block');
  }
}
