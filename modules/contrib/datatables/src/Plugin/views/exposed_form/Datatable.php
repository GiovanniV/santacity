<?php

/**
 * @file
 * Contains \Drupal\datatables\Plugin\views\exposed_form\Datatable.
 */

namespace Drupal\datatables\Plugin\views\exposed_form;

use Drupal\views\Plugin\views\exposed_form\ExposedFormPluginBase;

/**
 * Exposed form plugin that provides an exposed ("pseudo") form "connected"
 * to a datatables style plugin.
 *
 * @todo: we should be able to configure whether it's a "proper" form or just
 * a "<div>-container".
 *
 * @ingroup views_exposed_form_plugins
 *
 * @ViewsExposedForm(
 *   id = "datatables",
 *   title = @Translation("Datatables"),
 *   help = @Translation("Exposed form plugin connecting the values to the datatables style plugin")
 * )
 */
class Datatable extends ExposedFormPluginBase {
  /**
   * {@inheritdoc}
   */
  public function renderExposedForm($block = FALSE) {
    $form = parent::renderExposedForm($block);
    $form['#attached']['drupalSettings']['datatables'] = [
      $this->view->dom_id => [
        'exposed_form' => TRUE,
      ]
    ];
    $form['#theme_wrappers'] = ['datatables_exposed_form'];
    return $form;
  }
}
