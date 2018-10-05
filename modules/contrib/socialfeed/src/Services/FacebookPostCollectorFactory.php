<?php

namespace Drupal\socialfeed\Services;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class FacebookPostCollectorFactory.
 *
 * @package Drupal\socialfeed
 */
class FacebookPostCollectorFactory {

  /**
   * Default Facebook application id.
   *
   * @var string
   */
  protected $defaultAppId;

  /**
   * Default Facebook application secret.
   *
   * @var string
   */
  protected $defaultAppSecret;

  /**
   * FacebookPostCollector constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $config = $configFactory->get('socialfeed.facebooksettings');
    $this->defaultAppId = $config->get('app_id');
    $this->defaultAppSecret = $config->get('secret_key');
  }

  /**
   *
   */
  public function createInstance($appId, $appSecret) {
    return new FacebookPostCollector(
      $appId ?: $this->defaultAppId,
      $appSecret ?: $this->defaultAppSecret
    );
  }

}
