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
    $node = \Drupal::routeMatch()->getParameter('node');

    if( !(is_null($node))) {
      $nid = $node->id();
    } else {
      $nid = 0;
    }

    $form['testimonial'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Votre témoignage'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Soumettre'),
    ];

    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $testimonial = $form_state->getValue('testimonial');

    if (empty($testimonial)) {
      $form_state->setErrorByName('testimonial', $this->t('Le champ témoignage est obligatoire.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $uid = \Drupal::currentUser()->id();
      $name = \Drupal::currentUser()->getAccount()->getDisplayName();
      $nid = $form_state->getValue('nid');
      $testimonial = $form_state->getValue('testimonial');
      $current_time = \Drupal::time()->getRequestTime();

      $query = \Drupal::database()->insert('testimonial');

      $query->fields([
        'uid',
        'name',
        'nid',
        'testimonial',
        'created',
      ]);

      $query->values([
        $uid,
        $name,
        $nid,
        $testimonial,
        $current_time,
      ]);

      $query->execute();

      \Drupal::messenger()->addMessage(t("Votre témoignage a bien été enregistré !"));
    } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $e) {
      \Drupal::messenger()->addError(t
      ("Votre témoignage n'a pas pu être enregistré en raison d'une erreur de base de données.
       Veuillez réessayer ultérieurement."));
    } catch (\Exception $e) {
      \Drupal::messenger()->addError(t
      ("Une erreur inattendue s'est produite. Veuillez réessayer ultérieurement.
       Message d'erreur : @message", ['@message' => $e->getMessage()]));
    }
  }
}
