<?php

namespace Drupal\mrmilu_import\Processor;

use Drupal\node\Entity\Node;
use Drupal\redirect\Entity\Redirect;

class AliasProcessor {

  public function setRedirect(Node $node, $oldAlias) {
    Redirect::create([
      'redirect_source' => $oldAlias,
      'redirect_redirect' => 'internal:/node/' . $node->id(),
      'status_code' => 301,
    ])->save();
  }
}
