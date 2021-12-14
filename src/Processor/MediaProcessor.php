<?php

namespace Drupal\mrmilu_import\Processor;

use Drupal\Core\File\FileSystemInterface;
use Drupal\media\Entity\Media;

class MediaProcessor {

  private function fileDestination($entityType, $bundle, $fieldName) {
    $entityManager = \Drupal::service('entity_field.manager');
    $fields = $entityManager->getFieldDefinitions($entityType, $bundle);
    $settings = $fields[$fieldName]->getSettings();

    $directory = 'public://' . $settings['file_directory'];
    $year = date('Y');
    $month = date('m');

    $directory = str_ireplace('[date:custom:Y]', $year, $directory);
    $directory = str_ireplace('[date:custom:m]', $month, $directory);
    \Drupal::service('file_system')->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);

    return $directory . '/';
  }

  private function getFilename($filePath) {
    $filename = explode('/', $filePath);
    return end($filename);
  }

  public function processInternal($filename, $subfolder) {
    $filePath = join('/', [$subfolder, $filename]);
    $doc = file_get_contents($filePath);
    $file = file_save_data($doc, 'public://' . $filename);
    return $file->id();
  }

  public function processExternal($filePath, $entityType, $bundle, $fieldName) {
    $filename = $this->getFilename($filePath);

    $doc = file_get_contents($filePath);
    $destination = $this->fileDestination($entityType, $bundle, $fieldName);
    $file = file_save_data($doc, $destination . $filename);
    return $file->id();
  }

  public function createMediaFromPath($filePath, $bundle, $fieldName) {
    $fileID = $this->processExternal($filePath, 'media', $bundle, $fieldName);
    $fileName = $this->getFilename($filePath);

    return $this->createMedia($fileID, $fileName, $bundle, $fieldName);
  }

  public function createMediaFromDrive($files, $fileDriveID, $bundle, $fieldName) {
    $fileProperties = $files[$fileDriveID];
    $destination = $this->fileDestination('media', $bundle, $fieldName);
    $file = file_save_data($fileProperties['content'], $destination . $fileProperties['filename']);

    return $this->createMedia($file->id(), $fileProperties['filename'], $bundle, $fieldName);
  }

  private function createMedia($fileID, $fileName, $bundle, $fieldName) {
    $media = Media::create([
      'bundle' => $bundle,
      'uid' => \Drupal::currentUser()->id(),
      $fieldName => [
        'target_id' => $fileID
      ]
    ]);
    $media->setName($fileName)->setPublished()->save();

    return $media->id();
  }
}
