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
    $testimonials = $this->loadTestimonials(3);

    return [
      '#theme' => 'block_testimonial',
      '#testimonials' => $testimonials,
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.path'],
      ],
      '#attached' => [
        'library' => [
          'testimonial/testimonial',
        ],
      ],
    ];
  }

  protected function loadTestimonials($limit = 3) {
    // Initialize an empty array to store testimonial objects
    $testimonials = [];

    $current_node = \Drupal::routeMatch()->getParameter('node');
    $current_nid = $current_node ? $current_node->id() : NULL;

    if ($current_nid) {
      $query = \Drupal::database()->select('testimonial', 't');
      $query->fields('t', ['name', 'testimonial', 'created']);
      $query->orderBy('created', 'DESC'); // From most recent to oldest
      $query->condition('nid', $current_nid);
      $query->range(0, $limit); // Limit the number of results
      $results = $query->execute()->fetchAll();

      // Process each testimonial result
      foreach ($results as $result) {
        // Create a testimonial object and populate its properties
        $testimonial = new \stdClass();
        $testimonial->name = $result->name;
        $testimonial->testimonial = $result->testimonial;
        $testimonial->created = $result->created;

        // Add the testimonial object to the array
        $testimonials[] = $testimonial;
      }

      // Return the array of testimonial objects
      return $testimonials;
    }
    return NULL;
  }
}
