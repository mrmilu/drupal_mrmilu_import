<?php

namespace Drupal\mrmilu_import\Processor;

class FieldListProcessor {

  public function getOptions($fieldName, $entityType, $bundle) {
    $entityManager = \Drupal::service('entity_field.manager');
    $fields = $entityManager->getFieldStorageDefinitions($entityType, $bundle);
    return options_allowed_values($fields[$fieldName]);
  }

  public function getKeyFromValue($fieldName, $entityType, $bundle, $value) {
    $options = $this->getOptions($fieldName, $entityType, $bundle);
    if ($options && array_search($value, $options)) {
      return array_search($value, $options);
    }

    return NULL;
  }
}
