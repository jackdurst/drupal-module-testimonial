<?php

namespace  Drupal\testimonial\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestimonialController extends ControllerBase {

  public function loadMoreTestimonials() {
    try {
      $offset = \Drupal::request()->query->get('offset', 0);
      $limit = \Drupal::request()->query->get('limit', 1);
      $currentNodeId = \Drupal::request()->query->get('currentNodeId');

      if (!isset($currentNodeId)) {
        throw new \Exception('CurrentNodeId is not set.');
      }

      $testimonials = $this->loadTestimonials($offset, $limit, $currentNodeId);
      $hasMoreTestimonials = $this->hasMoreTestimonials($offset + $limit, $currentNodeId);

      $response = new JsonResponse([
        'testimonials' => $testimonials,
        'has_more_testimonials' => $hasMoreTestimonials,
      ]);

      return $response;
    } catch (\Exception $e) {
      // Log or handle the exception as needed
      return new JsonResponse([
        'testimonials' => $testimonials,
        'has_more_testimonials' => $hasMoreTestimonials,
      ]);
    }
  }

  protected function loadTestimonials($offset, $limit, $currentNodeId) {
    $testimonials = [];

    // Vérifier si $currentNodeId est défini
    if (!isset($currentNodeId)) {
      return $testimonials;
    }

    try {
      $query = \Drupal::database()->select('testimonial', 't');
      $query->fields('t', ['name', 'testimonial', 'created']);
      $query->orderBy('created', 'DESC');
      $query->condition('nid', $currentNodeId);
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
    } catch (\Exception $e) {
      return new JsonResponse(['error' => 'Erreur lors de la récupération en bdd du controller loadMoreTestimonials.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $testimonials;
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
