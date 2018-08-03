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
class XmlListingForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xml_listing_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['xml'] = array(
			'#type' => 'table',
			'#header' => array(t('Resource'), t('Type'), t('Download'), t('Preview')),
		);
		
		$records = $this->getXmlFiles();
		
		foreach ($records as $record) {
			// Resources
			$form['xml'][$record->id]['resource'] = array(
				'#plain_text' => $record->type,
			);
			
			// Type
			$form['xml'][$record->id]['type'] = array(
				'#plain_text' => 'csv',
			);
			
			// Download
			$form['xml'][$record->id]['download'] = array(
				'#type' => 'link',
				'#title' => $this->t('Download'),
				'#url' => Url::fromRoute('file_download.link', array('scheme' => 'public', 'fid' => $record->file_id)),
			);
			
			// Preview
			$form['xml'][$record->id]['preview'] = array(
				'#type' => 'radios',
				'#name' => 'preview',
				'#options' => [$record->file_id => $record->file_id],
				'#fid' => $record->file_id,
				'#ajax' => [
					'callback' => '::xmlPreview',
					'wrapper' => 'xml-table-content',
					'progress' => [
						'type' => 'throbber',
						'message' => t('Loading Record...'),
					],
				],
			);
		}
		
		$form['xml_records'] = [
			'#prefix' => '<div id="xml-table-content">',
			'#suffix' => '</div>',
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
    
  }
	
  /**
   * {@inheritdoc}
   */
  public function xmlPreview(array &$form, FormStateInterface $form_state) {
    $element = $form_state->getTriggeringElement();
		
		$tableContent = '';
		if(isset($element['#value']) && !empty($element['#value'])) {
			$tableContent = $this->loadXmlRecordsTable($element['#value']);
		}
		
		
		$form['xml_records'] = [
			'#prefix' => '<div id="xml-table-content">',
			'#suffix' => '</div>',
		];
		
		$form['xml_records']['tables'] = $tableContent;
		
		$form_state->setRebuild(TRUE);
		
		return $form['xml_records'];
  }
	
	/**
	 *
	*/
	public function loadXmlRecordsTable($fid) {
		$file = File::load($fid);
    $xmlPath = file_create_url($file->getFileUri());
		
		$xml = simplexml_load_file($xmlPath);
		
		$header = $rows = [];
		$item = 0;
		$itemLimit = 2;
		foreach($xml->children() as $record) {
			if($item > $itemLimit)
				break;;
			$record = json_decode(json_encode($record));
			$header = array_keys($record);
			$rows[] = [$record];
			$item++;
		}
		
		$form = [
			'#type' => 'table',
			'#header' => $header,
			'#rows' => $rows,
		];
		return $form;
	} 
	
  /**
  * Get XML Files
  */
  public function getXmlFiles($nid = '') {
    $connection = Database::getConnection();
    $query = $connection->select('xml_upload', 'xml');
    $query->fields('xml');
    $results = $query->execute();
    return $results;
    
  }

}
