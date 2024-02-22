<?php

/**
 * @file
 * Creates a block which displays the RSVPForm contained in RSVPForm.php.
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides the RSVP main Block.
 *
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("The RSVP Block")
 * )
 */
class RSVPBlock extends BlockBase {
  /**
   * @inheritdoc
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  /**
   * @inheritdoc
   */
  public function blockAccess(AccountInterface $account) {
    // if viewing a node, get the fully loaded node object.
    $node = \Drupal::routeMatch()->getParameter('node');

    if ( !(is_null($node))) {
      $enabler = \Drupal::service('rsvplist.enabler');
      if ($enabler->isenabled($node)) {
        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
      }
    }

    return AccessResult::forbidden();
  }
}
