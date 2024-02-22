<?php

/**
 * @file
 *
 */

namespace Drupal\testimonial\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestimonialSubmitForm extends FormBase {

  protected AccountInterface $currentUser;

  /**
   * Constructs a TestimonialSubmitForm object.
   * Reminder to myself : __construct is used to inject dependencies like the current_user service.
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * NOTE : J'ai vraiment du mal à saisir la nuance entre __construct() et create()
   *
   * la méthode create() est utilisée pour créer une instance de la classe de formulaire
   * au moment de son utilisation, généralement lors du rendu... ?
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'testimonial_submit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['testimonial'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Votre témoignage'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Soumettre'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation logic here
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Submission logic here
  }
}
