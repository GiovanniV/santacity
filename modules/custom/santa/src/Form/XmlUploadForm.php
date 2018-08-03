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
			'#type' => 'fieldset',
			'#title' => 'XML File Upload',
		];

		$form['xml_file_upload']['sample_xml'] = array(
			'#type' => 'link',
			'#title' => t('Sample XML'),
			'#attributes' => ['class' => ['pull-right']],
			'#url' => Url::fromRoute('file_download.link', array('scheme' => 'public', 'fid' => 905)),
		);
		
		$form['xml_file_upload']['name'] = array(
			'#type' => 'select',
			'#title' => t('Type'),
			'#required' => true,
			'#options' => [
				'Business Applications' => 'Business Applications',
				'Business Existing more than a year' => 'Business Existing more than a year',
				'Active Business' => 'Active Business',
			],
		);
		
		$form['xml_file_upload']['fid'] = array(
			'#type' => 'managed_file',
			'#title' => t('XML File'),
			'#upload_location' => 'public://xml/',
			'#required' => true,
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
		$this->saveXmlFile($inputData);
  }

  /**
  * Save Cultural Values
  **/
  private function saveXmlFile($inputValues) {
    $connection = Database::getConnection();
		
		$fieldValues = [
			'id' => '',
			'name' => $inputValues['name'],
			'type' => 'xml',
			'file_id' => $inputValues['fid'],
			'created_date' => time(),
		];
		
		dpm($fieldValues);
		if(empty($fieldValues['id'])) {
			$connection->insert('xml_upload')
				->fields($fieldValues)
				->execute();
			
		}
		else {
			
		}
  }

}
