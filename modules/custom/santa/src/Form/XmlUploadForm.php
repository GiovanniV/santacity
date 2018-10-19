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
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL, $fid = NULL) {
    $form = [];
		
		$form['xml_file_upload'] = [
			'#type' => 'fieldset',
			'#title' => 'XML File Upload',
		];

		$form['xml_file_upload']['sample_xml'] = array(
			'#type' => 'link',
			'#title' => t('Sample Data'),
			'#attributes' => ['class' => ['pull-right']],
			'#url' => Url::fromRoute('file_download.link', array('scheme' => 'public', 'fid' => $fid)),
		);
		
		$form['xml_file_upload']['fid'] = array(
			'#type' => 'managed_file',
			'#title' => t('XML File'),
			'#upload_location' => 'public://xml/data',
			'#required' => true,
			'#multiple' => false,
			'#description' => t('Allowed extensions: xml csv'),
			'#upload_validators' => array(
				'file_validate_extensions' => array('xml csv'),
			),
		);
		
		$form['xml_file_upload']['nid'] = [
			'#type' => 'value',
			'#value' => $nid,
		];
		
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
		
		if(!empty($inputData['fid'][0])) {
			$file = File::load($inputData['fid'][0]);
			$file->setPermanent();
			$file->save();
		}
		
		$this->saveXmlFile($inputData);
  }

  /**
  * Save Cultural Values
  **/
  private function saveXmlFile($inputValues) {
    $connection = Database::getConnection();
		$file = File::load($inputValues['fid'][0]);
		$type = $file->getMimeType();
		
		$fieldValues = [
			'id' => '',
			'nid' => $inputValues['nid'],
			'type' => $type,
			'file_id' => $inputValues['fid'][0],
			'created_date' => time(),
		];
		
		if(empty($fieldValues['id'])) {
			unset($fieldValues['id']);
			$connection->insert('santa_xml_upload')
				->fields($fieldValues)
				->execute();
			
		}
		else {
			
		}
  }
	
	
}
