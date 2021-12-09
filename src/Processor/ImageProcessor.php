<?php

namespace Drupal\mrmilu_import\Processor;

class ImageProcessor {

  public function processInternal($filename, $subfolder) {
    $filePath = join('/', [$subfolder, $filename]);
    $doc = file_get_contents($filePath);
    $file = file_save_data($doc, 'public://' . $filename);
    return $file->id();
  }

  public function processExternal($filePath) {
    $filename = explode('/', $filePath);
    $filename = end($filename);

    $doc = file_get_contents($filePath);
    $file = file_save_data($doc, 'public://' . $filename);
    return $file->id();
  }
}
