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
		dpm($records);
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
		
		$form['xml_form'] = [
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
		$fid = isset($element['#fid']) ? $element['#fid'] : '';
		
		$form = [
			'#prefix' => '<div id="xml-table-content">',
			'#suffix' => '</div>',
			'#markup' => $this->loadXmlRecordsTable($fid),
		];
		
		;
		$form_state->setRebuild(TRUE);
		
		return $form;
  }
	
	/**
	 *
	*/
	public function loadXmlRecordsTable($fid) {
		$file =File::load($fid);
    $path = file_create_url($file->getFileUri());
		
		return time();
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
