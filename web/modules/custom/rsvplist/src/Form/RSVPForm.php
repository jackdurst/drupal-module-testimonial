<?php

/**
 * @file
 * a form to collect an email for RSVP details
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase {
   /**
    * @{inheritdoc},
    */
   public function getFormId(): string
   {
     return 'rsvplist_email_form';
   }

  /**
   * @{inheritdoc},
   */
  public function buildForm(array $form, FormStateInterface $form_state): array
  {

    $node = \Drupal::routeMatch()->getParameter('node');

    if( !(is_null($node))) {
      $nid = $node->id();
    } else {
      $nid = 0;
    }

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t("Recevez en avant-première les dernières nouvelles !"),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * @{inheritdoc},
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  $value = $form_state->getValue('email');
   if(!(\Drupal::service('email.validator')->isValid($value))) {
     $form_state->setErrorByName('email',
     $this->t("Heuu mon bilou, %mail n'est pas un mail valide", ['%mail' => $value]));
   }
  }

  /**
   * @{inheritdoc},
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void
  {
//    $submitted_email = $form_state->getValue('email');
//    $this->messenger()->addMessage(t("Le formulaire fonctionne, vous avez entré @entry",
//      ['@entry' => $submitted_email]));

    try {
      // PHASE 1 : initiate values to save
      $uid = \Drupal::currentUser()->id();
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');
      $current_time = \Drupal::time()->getRequestTime();

      // PHASE 2 : save the values to the database
      $query = \Drupal::database()->insert('rsvplist');

      //specify the fields that the query will insert into
      $query->fields([
        'uid',
        'nid',
        'mail',
        'created',
      ]);

      //set the values of the fields we selected. ORDER IS IMPORTANT
      $query->values([
        $uid,
        $nid,
        $email,
        $current_time,
      ]);

      //execute the query
      $query->execute();

      \Drupal::messenger()->addMessage(t("Bien ouej t'es inscrit mon bichon"));


    } catch (\Exception $e) {
      \Drupal::messenger()->addError(t("Unable to sauvegarder ya un soucis bref"));
    }
  }
}
