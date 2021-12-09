<?php

namespace Drupal\mrmilu_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use Drupal\mrmilu_import\Import\Reader;

class MrMiluImportForm extends FormBase {

  private $credentialsPath;
  private $client;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mrmilu_import_form';
  }

  public function __construct() {
    $reader = new Reader();
    $this->credentialsPath = Settings::get('drive_credentials');  //token.json
    $this->client = $reader->getClient();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if (file_exists($this->credentialsPath)) {
      $accessToken = file_get_contents($this->credentialsPath);
      $this->client->setAccessToken($accessToken);
    }

    if (!file_exists($this->credentialsPath) || $this->client->isAccessTokenExpired()) {
      $authUrl = $this->client->createAuthUrl();

      $form['drive_markup'] = [
        '#type' => 'markup',
        '#markup' => t('Click <a href="@url">here</a> and paste code param', ['@url' => $authUrl])
      ];

      $form['drive_token'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Google Drive token'),
        '#default_value' => \Drupal::state()->get('drive_token'),
      ];

      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save')
      ];
    }
    else {
      $form['drive_markup'] = [
        '#type' => 'markup',
        '#markup' => t('token.json is configured')
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $token = $form_state->getValue('drive_token');
    $authCode = trim($token);

    // Exchange authorization code for an access token.
    $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
    file_put_contents($this->credentialsPath, json_encode($accessToken));

    $this->messenger()->addStatus($this->t('The configuration options have been saved.'));
  }
}
