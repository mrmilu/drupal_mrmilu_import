<?php

namespace Drupal\mrmilu_import\Import;

use Drupal\Core\Site\Settings;
use Google\Client;
use Google_Service_Drive;
use Google_Service_Sheets;

class Reader {

  protected $authConfigFile;
  protected $credentialsPath;
  protected $client;
  protected $gsheets;
  protected $driveService;
  protected $files;

  /**
   * @throws \Google\Exception
   */
  public function __construct() {
    $this->credentialsPath = Settings::get('drive_credentials');  //token.json
    $this->authConfigFile = Settings::get('drive_auth_config',);  //credentials.json
    $this->client = $this->initClient();
    $this->gsheets = new Google_Service_Sheets($this->client);
    $this->driveService = new Google_Service_Drive($this->client);
    $this->files = $this->driveService->files;
  }

  /**
   * @throws \Google\Exception
   */
  public function initClient()
  {
    $client = new Client();
    $client->setApplicationName('Mrmilu importer');
    $client->setScopes(Google_Service_Drive::DRIVE_READONLY);
    $client->setAuthConfig($this->authConfigFile);
    $client->setAccessType('offline');

    if (file_exists($this->credentialsPath)) {
      $accessToken = file_get_contents($this->credentialsPath);
      $client->setAccessToken($accessToken);

      // Refresh the token if it's expired.
      if ($client->isAccessTokenExpired()) {
        $client->refreshToken($accessToken);

        $accessToken = $client->getAccessToken();
        file_put_contents($this->credentialsPath, json_encode($accessToken));
      }
    }

    return $client;
  }

  public function getClient() {
    return $this->client;
  }

  protected function getSpreadsheetId($spreadsheetId) {
    return "$spreadsheetId";
  }

  public function getData($spreadsheetId, $sheetIndex, $range) {
    $response = $this->gsheets->spreadsheets->get($spreadsheetId);
    $sheetName = $response->getSheets()[$sheetIndex]->getProperties()->getTitle();
    $r = "$sheetName!$range";

    $response = $this->gsheets->spreadsheets_values->get($spreadsheetId, $r);
    return $response->getValues();
  }

  public function getFile($fileID) {
    $optParams = [
      'supportsAllDrives' => true,
      'supportsTeamDrives' => true,
    ];

    $fileDrive = $this->files->get($fileID, $optParams);
    $fileResponse = $this->files->get($fileID, ['alt' => 'media']);

    return [
      'filename' => $fileDrive->getName(),
      'content' => $fileResponse->getBody()->getContents()
    ];
  }
}
