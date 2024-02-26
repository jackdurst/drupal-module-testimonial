<?php

/**
 * @file
 * Creates a block which displays the Testimonials.
 */

namespace Drupal\testimonial\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides the TestimonialSubmitForm main Block.
 *
 * @Block(
 *   id = "testimonial_display_block",
 *   admin_label = @Translation("The Testimonial Display Block")
 * )
 */
class TestimonialDisplayBlock extends BlockBase {
  public function build() {
    $testimonials = [];

    $query = \Drupal::database()->select('testimonial', 't');
    $query->fields('t', ['name', 'testimonial', 'created']);
//    $query->range(0, 2); // Adjust the range to display 3 testimonials at a time.
    $query->orderBy('created', 'DESC'); // From last to first
    $results = $query->execute()->fetchAll();

    // Load testimonial nodes.
    foreach ($results as $result) {
      $testimonials[] = [
        'name' => $result->name,
        'testimonial' => $result->testimonial,
        'date' => date('(d/m/Y)', $result->created),
      ];
    }

    kint($testimonials);

    $build = [
      '#theme' => 'testimonial_display_block',
      '#testimonials' => $testimonials,
    ];


    return $build;
  }
}
