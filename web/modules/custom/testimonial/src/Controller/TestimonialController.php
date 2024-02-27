<?php

namespace  Drupal\testimonial\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestimonialController extends ControllerBase {

  public function loadMoreTestimonials() {
    $offset = \Drupal::request()->query->get('offset', 0); // A partir duquel
    $limit = \Drupal::request()->query->get('limit', 5); // Combien d'élément

    $testimonials = $this->loadTestimonials($offset, $limit);

    $hasMoreTestimonials = $this->hasMoreTestimonials($offset + $limit);

    $response = new JsonResponse([
      'testimonials' => $testimonials,
      'has_more_testimonials' => $hasMoreTestimonials,
    ]);

    return $response;
  }

  protected function loadTestimonials($offset, $limit) {
    $testimonials = [];

    $query = \Drupal::database()->select('testimonial', 't');
    $query->fields('t', ['name', 'testimonial', 'created']);
    $query->orderBy('created', 'DESC');
    $query->range($offset, $limit);
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      $testimonial = [
        'name' => $result->name,
        'testimonial' => $result->testimonial,
        'created' => $result->created,
      ];
      $testimonials[] = $testimonial;
    }

    return $testimonials;
  }

  protected function hasMoreTestimonials($nextOffset) {
    $totalTestimonials = $this->getTotalTestimonialsCount();

    return $totalTestimonials > $nextOffset;
  }

  protected function getTotalTestimonialsCount() {
    $query = \Drupal::database()->select('testimonial', 't');
    $query->addExpression('COUNT(*)');
    return $query->execute()->fetchField();
  }
  }
