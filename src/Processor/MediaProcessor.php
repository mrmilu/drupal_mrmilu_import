<?php

namespace Drupal\mrmilu_import\Processor;

use Drupal\Core\File\FileSystemInterface;
use Drupal\media\Entity\Media;

class MediaProcessor {

  public function createMediaFromPath($filePath, $bundle, $fieldName) {
    $fileID = $this->processExternal($filePath, 'media', $bundle, $fieldName);
    $fileName = $this->getFilename($filePath);

    return $this->createMedia($fileID, $fileName, $bundle, $fieldName);
  }

  public function createMediaFromDrive($fileProperties, $bundle, $fieldName) {
    $destination = $this->fileDestination('media', $bundle, $fieldName);
    $file = file_save_data($fileProperties['content'], $destination . $fileProperties['filename']);

    return $this->createMedia($file->id(), $fileProperties['filename'], $bundle, $fieldName);
  }

  public function createFileFromDrive($fileProperties, $entityType, $bundle, $fieldName) {
    $destination = $this->fileDestination($entityType, $bundle, $fieldName);
    $file = file_save_data($fileProperties['content'], $destination . $fileProperties['filename']);
    return $file->id();
  }

  public function ogPathFromDrive($fileProperties) {
    $directory = 'public://og';
    \Drupal::service('file_system')->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $file = file_save_data($fileProperties['content'], $directory . '/' .$fileProperties['filename']);

    if ($file) {
      $fileUri = $file->getFileUri();
      return file_url_transform_relative(file_create_url($fileUri));
    }
    return NULL;
  }

  /**
   * Private functions
   */
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

  private function processExternal($filePath, $entityType, $bundle, $fieldName) {
    $filename = $this->getFilename($filePath);

    $doc = file_get_contents($filePath);
    $destination = $this->fileDestination($entityType, $bundle, $fieldName);
    $file = file_save_data($doc, $destination . $filename);
    return $file->id();
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
