<?php
/**
 * @file
 * Contains \Drupal\amazing_forms\Form\ContributeForm.
 */

namespace Drupal\santa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Contribute form.
 */
class ExampleForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['example'] = array(
  '#type' => 'datatable',
  '#draggable' => TRUE,
  '#limit' => 25, // Use 0 for unlimited
  '#structure' => array(
    array(
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => NULL,
    ),
    array(
      '#type' => 'select',
      '#title' => t('Favorite Color'),
      '#default_value' => 'red',
      '#options' => array(
        'red' => 'Red', 
        'green' => 'Green', 
        'blue' => 'Blue'
      ),
    ),
    array(
      '#type' => 'checkbox',
      '#title' => t('Likes Cats'),
      '#return_value' => 1,
      '#default_value' => 0,
    )
  ),
  '#value' => array(
    array('John Doe', 'green', 0),
    array('Lauren Ipsum', 'red', 1),
    array('Test', 'blue', 1),
  ),
);
    return $form;
  }

  // Datatable options example.
  public function getDataTablesOptions() {
    return [
      // Allow retrieve DataTable.
      'retrieve' => TRUE,
      'exposed_form' => TRUE,
      // State Save.
      'stateSave' => TRUE,
      // When set to true used data-drupal-selector (Useful for Ajax processed(replaced) tables).
      'stateAlternativeSave' => FALSE,
      // When set to -1 sessionStorage will be used, while for 0 or greater localStorage will be used.
      // The value 0 is a special value as it indicates that the state can be stored and retrieved
      // indefinitely with no time limit. Example (1 day):  'stateDuration' => 60 * 60 * 24
      'stateDuration' => 0,
      'deferRender' => FALSE,
      'processing' => TRUE,
      'filter' => FALSE,
      'info' => FALSE,
      'collapsedClass' => FALSE,
      'ordering' => TRUE,
      'lengthChange' => TRUE,
      'displayLength' => 10,
      'pageLength' => 10,
      'dom' => '<B<"datatables-header"ilf>rtp',
      'buttons' => [
        [
          'extend' => 'colvis',
          'collectionLayout' => 'fixed two-column',
          'text' => 'Show/Hide Columns',
        ],
        [
          'extend' => 'print',
          'text' => 'Print',
          'exportOptions' => [
            'modifier' => [
              'page' => 'current',
            ],
            'orientation' => 'landscape',
            'pageSize' => 'A4',
            'columns' => ':visible:not([aria-label="Operations"])',
          ],
        ],
        [
          'extend' => 'collection',
          'name' => 'primary',
          'collectionLayout' => 'fixed',
          'text' => 'Export',
          'className' => 'buttons-excel',
          'buttons' => [
            [
              'extend' => 'pdfHtml5',
              'text' => 'Export to PDF',
              'orientation' => 'landscape',
              'pageSize' => 'A4',
              'exportOptions' => [
                'modifier' => [
                  'page' => 'current',
                ],
                'columns' => ':visible:not([aria-label="Operations"])',
              ],
            ],
            [
              'extend' => 'excelHtml5',
              'text' => 'Export to Excel',
              'exportOptions' => [
                'modifier' => [
                  'page' => 'current',
                ],
                'columns' => ':visible:not([aria-label="Operations"])',
              ],
            ],
          ],
        ],
      ],
      'scrollY' => 480,
      'scrollX' => '100%',
      'scrollCollapse' => TRUE,
      'paging' => FALSE,
      'order' => [
        [0, 'asc'],
        [1, 'asc'],
        [2, 'asc'],
      ],
      'fixedColumns' => [
        'leftColumns' => 2,
        'heightMatch' => 'semiauto',
      ],
      // Disable autoWidth.
      'autoWidth' => FALSE,
      'language' => [
        'processing' => '<div class="your-class"><div class="your-progress-throbber"></div></div>',
        'decimal' => '.',
        'thousands' => ',',
        'paginate' => [
          'first' => '« First',
          'last' => 'Last »',
          'next' => '››',
          'previous' => '‹‹',
        ],
        'info' => 'Showing _START_-_END_ out of _TOTAL_',
      ],
      'lengthMenu' => [],
    ];
  }

	
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
