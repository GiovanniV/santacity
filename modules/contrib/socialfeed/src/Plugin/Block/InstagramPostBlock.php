<?php

namespace Drupal\socialfeed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\socialfeed\Services\InstagramPostCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides a 'InstagramPostBlock' block.
 *
 * @Block(
 *  id = "instagram_post_block",
 *  admin_label = @Translation("Instagram Block"),
 * )
 */
class InstagramPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Instagram Service.
   *
   * @var \Drupal\socialfeed\Services\InstagramPostCollector
   */
  protected $instagram;

  /**
   * InstagramPostBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The ConfigFactory $config_factory.
   * @param \Drupal\socialfeed\Services\InstagramPostCollector $instagram
   *   The InstagramPostCollector $instagram.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactory $config_factory, InstagramPostCollector $instagram) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get('socialfeed.instagramsettings');
    $this->instagram = $instagram;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Symfony\Component\DependencyInjection\ContainerInterface parameter.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('socialfeed.instagram')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Returning data as an array.
   */
  public function build() {
    $build = [];
    $posts = $this->instagram->getPosts($this->config->get('picture_count'), $this->config->get('picture_resolution'));

    foreach ($posts as $post) {
      $items[] = [
        '#theme' => 'socialfeed_instagram_post_' . $post['raw']->type,
        '#post' => $post,
        '#cache' => [
          // Cache for 1 hour.
          'max-age' => 60 * 60,
          'cache tags' => $this->config->getCacheTags(),
          'context' => $this->config->getCacheContexts(),
        ],
      ];
    }
    $build['posts'] = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
    return $build;
  }

}
