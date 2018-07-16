<?php

namespace Drupal\santa\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "Home Flexslider",
 *   admin_label = @Translation("Home Flexslider"),
 *   category = @Translation("santacity"),
 * )
 */
class Homeslider extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'home_slider',
      '#data' => [],
    );
  }

}
