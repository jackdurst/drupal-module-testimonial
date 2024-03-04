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
    $current_node = \Drupal::routeMatch()->getParameter('node');
    $current_nid = $current_node ? $current_node->id() : NULL;

    $testimonials = $this->loadTestimonials(3, $current_nid);
    $moreTesti = $this->hasMoreTestimonials(3, $current_nid);

    return [
      '#theme' => 'block_testimonial',
      '#testimonials' => $testimonials,
      '#has_more_testimonials' => $moreTesti,
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.path'],
      ],
      '#attached' => [
        'drupalSettings' => [
          'mynid' => $current_nid,
        ],
        'library' => [
          'testimonial/testimonial',
        ],
      ],
    ];
  }

  protected function loadTestimonials($limit, $current_nid) {
    // Initialize an empty array to store testimonial objects
    $testimonials = [];

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

  protected function hasMoreTestimonials($nextOffset, $currentNodeId) {
    $totalTestimonials = $this->getTotalTestimonialsCount($currentNodeId);

    if ($totalTestimonials > $nextOffset) {
      return true;
    } else {
      return false;
    }
  }

  protected function getTotalTestimonialsCount($currentNodeId) {
    $query = \Drupal::database()->select('testimonial', 't');
    $query->addExpression('COUNT(*)');
    $query->condition('t.nid', $currentNodeId);
    return $query->execute()->fetchField();
  }
}
