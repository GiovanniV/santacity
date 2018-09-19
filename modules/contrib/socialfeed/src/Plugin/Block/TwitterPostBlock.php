<?php

namespace Drupal\socialfeed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\socialfeed\Services\TwitterPostCollector;

/**
 * Provides a 'TwitterPostBlock' block.
 *
 * @Block(
 *  id = "twitter_post_block",
 *  admin_label = @Translation("Twitter Block"),
 * )
 */
class TwitterPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\socialfeed\Services\TwitterPostCollector definition.
   *
   * @var \Drupal\socialfeed\Services\TwitterPostCollector
   */
  protected $twitter;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * TwitterPostBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\socialfeed\Services\TwitterPostCollector $socialfeed_twitter
   *   The TwitterPostCollector $socialfeed_twitter.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The ConfigFactoryInterface $config.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TwitterPostCollector $socialfeed_twitter, ConfigFactoryInterface $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->twitter = $socialfeed_twitter;
    $this->config = $config->get('socialfeed.twittersettings');
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
      $container->get('socialfeed.twitter'),
      $container->get('config.factory')
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
    $posts = $this->twitter->getPosts($this->config->get('tweets_count'));
    foreach ($posts as $post) {
      $items[] = [
        '#theme' => 'socialfeed_twitter_post',
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
