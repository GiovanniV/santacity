<?php

namespace Drupal\socialfeed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\socialfeed\Services\FacebookPostCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'FacebookPostBlock' block.
 *
 * @Block(
 *  id = "facebook_post",
 *  admin_label = @Translation("Facebook Block"),
 * )
 */
class FacebookPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\socialfeed\Services\FacebookPostCollector defination.
   *
   * @var \Drupal\socialfeed\Services\FacebookPostCollector
   */
  protected $facebook;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * FacebookPostBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\socialfeed\Services\FacebookPostCollector $facebook
   *   The ConfigFactory $config_factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The InstagramPostCollector $instagram.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FacebookPostCollector $facebook, ConfigFactoryInterface $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->facebook = $facebook;
    $this->config = $config->get('socialfeed.facebooksettings');
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Returning data as an array.
   *
   * @throws \Facebook\Exceptions\FacebookSDKException
   */
  public function build() {
    $build = [];
    $items = [];
    $post_types = $this->config->get('all_types');
    if (!$post_types) {
      $post_types = $this->config->get('post_type');
    }
    $posts = $this->facebook->getPosts(
      $this->config->get('page_name'),
      $post_types,
      $this->config->get('no_feeds')
    );
    foreach ($posts as $post) {
      $items[] = [
        '#theme' => [
          'socialfeed_facebook_post__' . $post['type'],
          'socialfeed_facebook_post',
        ],
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

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Symfony\Component\DependencyInjection\ContainerInterface parameter.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin implementation definition.
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
      $container->get('socialfeed.facebook'),
      $container->get('config.factory')
    );
  }

}
