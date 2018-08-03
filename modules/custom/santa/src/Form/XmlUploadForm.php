<?php
/**
 * @file
 * Contains \Drupal\amazing_forms\Form\ContributeForm.
 */

namespace Drupal\santa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Contribute form.
 */
class XmlUploadForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_file_upload_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
		
		$form['xml_file_upload'] = [
			'#type' => 'container',
		];

		$form['xml_file_upload']['fid'] = array(
			'#type' => 'managed_file',
			'#title' => t('XML File'),
			'#upload_location' => 'public://xml/',
			'#description' => t('Allowed extensions: gif png jpg jpeg'),
			'#upload_validators' => array(
				'file_validate_extensions' => array('xml'),
			),
		);
		
		$form['xml_file_upload']['submit'] = [
			'#type' => 'submit',
			'#value' => 'Import',
		];
		
    return $form;
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
    $inputData = $form_state->getValues();

    $this-saveCultualValues($inputData);
  }

  /**
  * Save Cultural Values
  **/
  static function saveCultualValues($fieldValues) {
    $connection = Database::getConnection();
    $connection->merge('cultural_values')
      ->key(['cid' => $fieldValues['cid']])
      ->fields($fieldValues)
      ->execute();
  }

  /**
  * Get Cultural Values
  */
  public function getCulturalValue($nid = '') {
    if(empty($nid))
    return [];
    $connection = Database::getConnection();
    $query = $connection->select('cultural_values', 'cv');
    $query->fields('cv', array('country', 'data', 'uid'));
    $query->condition('cv.cid', $nid);
    $results = $query->execute()->fetchAssoc();
    return $results;
  }

}
