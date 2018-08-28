<?php

namespace Drupal\geolocation_google_maps\Plugin\geolocation\MapFeature;

use Drupal\geolocation\MapFeatureBase;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Provides context popup.
 *
 * @MapFeature(
 *   id = "context_popup",
 *   name = @Translation("Context Popup"),
 *   description = @Translation("Provide context / right-click popup window."),
 *   type = "google_maps",
 * )
 */
class ContextPopup extends MapFeatureBase {

  /**
   * {@inheritdoc}
   */
  public static function getDefaultSettings() {
    return [
      'content' => [
        'value' => '',
        'format' => filter_default_format(),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $settings, array $parents) {
    $settings = $this->getSettings($settings);

    $form = parent::getSettingsForm($settings, $parents);
    $form['content'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Context popup content'),
      '#description' => $this->t('A right click on the map will open a context popup with this content. Tokens supported. Additionally "@lat, @lng" will be replaced dynamically.'),
    ];
    if (!empty($settings['content']['value'])) {
      $form['content']['#default_value'] = $settings['content']['value'];
    }

    if (!empty($settings['content']['format'])) {
      $form['content']['#format'] = $settings['content']['format'];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function alterMap(array $render_array, array $feature_settings, array $context = []) {
    $render_array = parent::alterMap($render_array, $feature_settings, $context);

    $feature_settings = $this->getSettings($feature_settings);

    $token_context = [];
    if (!empty($context['view'])) {
      $token_context['view'] = $context['view'];
    }

    if (
      !empty($settings['content']['value'])
      && !empty($settings['content']['format'])
    ) {
      $content = check_markup(\Drupal::token()->replace($feature_settings['content']['value'], $token_context), $feature_settings['content']['format']);
    }
    else {
      return $render_array;
    }

    $render_array['#attached'] = BubbleableMetadata::mergeAttachments(
      empty($render_array['#attached']) ? [] : $render_array['#attached'],
      [
        'library' => [
          'geolocation_google_maps/geolocation.contextpopup',
        ],
        'drupalSettings' => [
          'geolocation' => [
            'maps' => [
              $render_array['#id'] => [
                'context_popup' => [
                  'enable' => TRUE,
                  'content' => $content,
                ],
              ],
            ],
          ],
        ],
      ]
    );

    return $render_array;
  }

}
