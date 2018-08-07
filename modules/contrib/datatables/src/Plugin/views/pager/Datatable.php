<?php

/**
 * @file
 * Contains \Drupal\datatables\Plugin\views\pager\Datatable.
 */

namespace Drupal\datatables\Plugin\views\pager;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\pager\PagerPluginBase;
/**
 * Plugin for views without pagers.
 *
 * @ingroup views_pager_plugins
 *
 * @ViewsPager(
 *   id = "datatable",
 *   title = @Translation("Datatable"),
 *   help = @Translation("Integration for the Datatable Plugin."),
 *   display_types = {"basic"}
 * )
 */
class Datatable extends PagerPluginBase {

  var $ajax = FALSE;

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    // Check if ajax is enabled
    if($view->ajaxEnabled() == TRUE) {
      $this->ajax = TRUE;
    }
  }

  public function summaryTitle() {
    return $this->t('Settings');
  }

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['pages'] = array(
      'default' => array(
        'pagination_style' => 0,
        'length_change' => 0,
        'display_length' => 10,
        'length_menu' => '[[10, 25, 50, -1], [10, 25, 50, "All"]]'
      ),
    );

    return $options;
  }

  /**
   * Provide the default form for setting options.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['pages'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Pagination and Page Length'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );

    $form['pages']['pagination_style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Pagination Style'),
      '#default_value' => isset($this->options['pages']['pagination_style']) ? $this->options['pages']['pagination_style'] : 0,
      '#options' => array(
        0 => $this->t('Two-Button (Default)'),
        'full_numbers' => $this->t('Full Numbers'),
        'no_pagination' => $this->t('No Pagination'),
      ),
      '#description' => $this->t('Selects which pagination style should be used.'),
    );

    $form['pages']['length_change'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Length Change Selection Box'),
      '#default_value' => !empty($this->options['pages']['length_change']),
      '#description' => $this->t('Enable or page length selection menu'),
    );

    $form['pages']['display_length'] = array(
      '#type' => 'select',
      '#title' => $this->t('Default Page Length'),
      '#default_value' => isset($this->options['pages']['display_length']) ? $this->options['pages']['display_length'] : 10,
      '#options' => array(10 => 10, 25 => 25, 50 => 50, 100 => 100),
      '#description' => $this->t('Default number of records to show per page. May be adjusted by users if Length Selection is enabled'),
    );

    $form['pages']['length_menu'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Default Length Menu'),
      '#default_value' => $this->options['pages']['length_menu'],
      '#description' => $this->t('Choose how many rows be visible in the table.The entry must follow the following schema [[10, 25, 50, -1], [10, 25, 50, "All"]]. In the first inner first brackets are the values and in the second inner bracket are the displayed values.'),
    );

  }

  /**
   * Helper function that checks both in request (i.e. POST) and query (i.e. GET).
   *
   * @param $key
   * @return mixed
   */
  protected function getRequestValue($key) {
    // POST overrides GET.
    return $this->view->getRequest()->request->has($key) ?
      $this->view->getRequest()->request->get($key) :
      $this->view->getRequest()->query->get($key);
  }

  public function getItemsPerPage() {
    // As default we deliver all rows
    $items_per_page =  0;

    if($this->ajax == TRUE){
      $limit = $this->getRequestValue('length');
      if(!$limit){
        $limit = $this->options['pages']['display_length'];
      }
      $items_per_page = $limit;
    }

    return $items_per_page;

  }

  public function query() {
    /**
     * Ajax paging
     * Limit rows and set the offset for the ajax response
     * Set offset
     */
    if($this->ajax == TRUE) {
      $offset = $this->getRequestValue('start');
      $limit = $this->getRequestValue('length');
      $total_items = $this->getRequestValue('total_items');

      if(!$offset){
        $offset = $this->getCurrentPage() * $limit;
      }

      $this->view->getQuery()->setOffset($offset);

      if(!$limit){
        $limit = $this->getItemsPerPage();
      }

      $this->view->getQuery()->setLimit($limit);

      $this->setCurrentPage($offset / $limit);
    }

  }

}
