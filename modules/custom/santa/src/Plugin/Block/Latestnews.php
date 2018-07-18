<?php

namespace Drupal\santa\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "Latest News Slider",
 *   admin_label = @Translation("Latest News Slider"),
 *   category = @Translation("santacity"),
 * )
 */
class Latestnews extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'latestnews_slider',
      '#data' => [],
    );
  }

}
