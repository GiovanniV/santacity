<?php

/**
 * @file
 * Contains \Drupal\dattables\Plugin\views\style\Table.
 */

namespace Drupal\datatables\Plugin\views\style;

use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\Table;

/**
 * Style plugin to render each item as a row in a datatable.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "datatable",
 *   title = @Translation("Datatable"),
 *   help = @Translation("Displays rows in a datatable."),
 *   theme = "datatable_view_table",
 *   display_types = {"normal"}
 * )
 */
class Datatable extends Table implements CacheableDependencyInterface {

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['elements'] = array(
      'default' => array(
        'search_box' => TRUE,
        'table_info' => TRUE,
        'save_state' => FALSE,
        'processing' => TRUE
      ),
    );
    $options['layout'] = array(
      'default' => array(
        'autowidth' => FALSE,
        'themeroller' => FALSE,
        'sdom' => ''
      ),
    );

    $options['plugins'] = array(
      'default' => array(
        'fixed_columns' => array(
          'left_columns' => 0,
          'right_columns' => 0,
        )
      ),
    );

    $options['styling'] = array(
      'default' => array(
        'theme' => 'default',
        'class' => '',
      )
    );

    $options['hidden_columns'] = array(
      'default' => array(),
    );

    return $options;
  }

  /**
   * Render the given style.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $handlers = $this->displayHandler->getHandlers('field');
    if (empty($handlers)) {
      $form['error_markup'] = array(
        '#markup' => '<div class="messages messages--error">' . $this->t('You need at least one field before you can configure your table settings') . '</div>',
      );

      return;
    }

    unset($form['sticky']);
    unset($form['override']);

    // Note: views UI registers this theme handler on our behalf. Your module
    // will have to register your theme handlers if you do stuff like this.
    $form['#theme'] = 'datatables_ui_style_plugin_table';

    $columns = $this->sanitizeColumns($this->options['columns']);

    foreach ($columns as $column_name => $column_label) {
      $form['info'][$column_name]['hidden_columns'] = array(
        '#type' => 'select',
        '#title' => Html::escape($column_label),
        '#title_display' => 'invisible',
        '#default_value' => isset($this->options['info'][$column_name]['hidden_columns']) ? $this->options['info'][$column_name]['hidden_columns'] : 0,
        '#options' => array(
          0 => 'Visible',
          'hidden' => 'Hidden',
          'expandable' => 'Hidden and Expandable',
        ),
      );
    }

    $form['description_markup']['#markup'] = '<div class="description form-item">' . $this->t('DataTables works best if you set the datatables pager, since DataTabels contains its own pager implementation.<br/><br/>Place fields into columns; you may combine multiple fields into the same column. If you do, the separator in the column specified will be used to separate the fields. Check the sortable box to make that column click sortable, and check the default sort radio to determine which column will be sorted by default, if any. You may control column order and field labels in the fields section.') . '</div>';

    //@TODO Add vertical tabs see https://www.drupal.org/node/2617924
    $form['datatables'] = array(
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Datatables'),
      '#default_tab' => 'edit-elements',
    );

    $form['elements'] = array(
      '#type' => 'details',
      '#title' => $this->t('Widgets & Elements'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#group' => 'datatables'
    );

    $form['elements']['search_box'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable the search filter box.'),
      '#default_value' => !empty($this->options['elements']['search_box']),
      '#description' => $this->t('The search filter box allows users to dynamically filter the results in the table.  Disabling this option will hide the search filter box from users.'),
    );
    $form['elements']['table_info'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable the table information display.'),
      '#default_value' => !empty($this->options['elements']['table_info']),
      '#description' => $this->t('This shows information about the data that is currently visible on the page, including information about filtered data if that action is being performed.'),
    );
    $form['elements']['save_state'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Save State'),
      '#default_value' => !empty($this->options['elements']['save_state']),
      '#description' => $this->t("DataTables can use cookies in the end user's web-browser in order to store it's state after each change in drawing. What this means is that if the user were to reload the page, the table should remain exactly as it was (length, filtering, pagination and sorting)"),
    );
    $form['elements']['processing'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Show processing'),
      '#default_value' => !empty($this->options['elements']['processing']),
      '#description' => $this->t("DataTables show a processing info during (length, filtering, pagination and sorting)"),
    );

    $form['layout'] = array(
      '#type' => 'details',
      '#title' => $this->t('Layout and Display'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#group' => 'datatables'
    );

    $form['layout']['autowidth'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable auto-width calculation.'),
      '#default_value' => !empty($this->options['layout']['autowidth']),
      '#description' => $this->t('Enable or disable automatic column width calculation. This can be disabled as an optimisation (it takes some time to calculate the widths).'),
    );

    $form['layout']['sdom'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Set sDOM Initialization Parameter'),
      '#default_value' => $this->options['layout']['sdom'],
      '#description' => $this->t("Use the sDOM parameter to rearrange the interface elements. See the <a href='@sdom_documentation_url'>Datatables sDOM documentation</a> for details on how to use this feature", array('@sdom_documentation_url' => 'http://www.datatables.net/examples/basic_init/dom.html')),
    );

    $form['layout']['scroll_y'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Scroll y'),
      '#default_value' => $this->options['layout']['scroll_y'],
    );

    $form['layout']['scroll_x'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Scroll x'),
      '#default_value' => $this->options['layout']['scroll_x'],
    );

    $form['layout']['scroll_collapse'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Scroll collapse'),
      '#default_value' => $this->options['layout']['scroll_collapse'],
    );

    $form['styling'] = array(
      '#type' => 'details',
      '#title' => $this->t('Styling'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'datatables'
    );

    $form['styling']['theme'] = array(
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#default_value' => isset($this->options['styling']['theme']) ? $this->options['styling']['theme'] : '',
      '#options' => array(
        'default' => $this->t('Default'),
        'bootstrap' => $this->t('Bootstrap'),
        'foundation' => $this->t('Foundation'),
        'jqueryui' => $this->t('jQuery UI'),
        'jqueryui_themeroller' => $this->t('jQuery UI Themeroller'),
        'custom' => $this->t('Custom'),
      ),
      '#description' => $this->t('Selects which theme style should be used.'),
    );

    $form['styling']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Set table classes'),
      '#default_value' => $this->options['styling']['class'],
      '#description' => $this->t("Use datatables classes to style the table. See the <a href='@styling_documentation_url'>Datatables styling documentation</a> for details on how to use this feature", array('@styling_documentation_url' => 'http://datatables.net/examples/styling/')),
    );

    $form['plugins'] = array(
      '#type' => 'details',
      '#title' => $this->t('Plugins'),
      '#group' => 'datatables'
    );

    $form['plugins']['fixed_columns'] = array(
      '#type' => 'details',
      '#title' => $this->t('Frozen columns'),
    );

    $form['plugins']['fixed_columns']['info']['#markup'] = '<div class="description form-item">' . $this->t('DataTables fixed column extension doesn\'t work with hidden or expandable columns.') . '</div>';

    $form['plugins']['fixed_columns']['left_columns'] = array(
      '#type' => 'number',
      '#title' => $this->t('Fixed left columns'),
      '#default_value' => $this->options['plugins']['fixed_columns']['left_columns'],
      '#min' => 0,
      '#prefix' => '<table style="width:120px;"><tr><td>',
      '#suffix' => '</td>'
    );

    $form['plugins']['fixed_columns']['right_columns'] = array(
      '#type' => 'number',
      '#title' => $this->t('Fixed right columns'),
      '#default_value' => $this->options['plugins']['fixed_columns']['right_columns'],
      '#min' => 0,
      '#prefix' => '<td>',
      '#suffix' => '</td></tr></table>'
    );

    $form['plugins']['select'] = array(
      '#type' => 'details',
      '#title' => $this->t('Selection'),
      '#description' => $this->t('The select plugin allows selection of rows, columns and cells - NOTE: currently only single row selection is implemented'),
    );

    $form['plugins']['select']['row'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable (Single) Row Select Plugin'),
      '#default_value' => !empty($this->options['plugins']['select']['row']),
      '#description' => $this->t('Allow row selection'),
    );

    $form['plugins']['select']['id_column_index'] = array(
      '#type' => 'select',
      '#title' => $this->t('ID Column'),
      '#options' => array_values($columns),
      '#default_value' => !empty($this->options['plugins']['select']['id_column'] ),
      '#description' => $this->t('Column serving as a source for selected id'),
    );

    $form['plugins']['select']['label_column_index'] = array(
      '#type' => 'select',
      '#title' => $this->t('Label Column'),
      '#options' => array_values($columns),
      '#default_value' => !empty($this->options['plugins']['select']['id_column'] ),
      '#description' => $this->t('Column serving as a source for label'),
    );
  }

  public function getRenderedFields(){
    return $this->rendered_fields;
  }
}
