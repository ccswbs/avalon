<?php

namespace Drupal\ug_theme\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Utility\Variables;

/**
 * @BootstrapPreprocess("region")
 */
class Region extends \Drupal\bootstrap\Plugin\Preprocess\Region {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);

    /**
     * Available region names:
     * @see ug_theme.info.yml from line 11
     */
    $region_name = $variables['elements']['#region'];

    if ($region_name == 'site_branding') {
      $variables->addClass(['navbar-header', 'flex-bottom']);
    }

    if ($region_name == 'navigation') {
      $variables->addClass(['navbar-collapse', 'collapse', 'flex-bottom', 'flex-right']);
      $variables->setAttribute("id", 'primary-nav');
    }

    if ($region_name == 'highlighted' && !empty($variables['content'])) {
      $variables->addClass(['highlighted', 'jumbotron']);
    }

    if ($region_name == 'content_top') {
      $variables->addClass(['row', 'search-and-breadcrumb']);
    }
  }

}
