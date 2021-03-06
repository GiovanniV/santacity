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
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
		$extensions = array('application/xml' => 'XML','text/csv' => 'CSV');
		
		$form['#attributes'] = [
			'class' => ['xml-listing-form-container'],
		];
		
		$current_user = \Drupal::currentUser();
		$userRole = $current_user->getRoles();
		
		$showOperation = 0;
		if(in_array('super_admin', $userRole) || in_array('administrator', $userRole)) {
			$showOperation = 1;
		}
		
		if($showOperation)
			$header = array(t('Resource'), t('Type'), t('Download'), t('Preview'), t('Operations'));
		else
			$header = array(t('Resource'), t('Type'), t('Download'), t('Preview'));
		
    $form['xml'] = array(
			'#type' => 'table',
			'#header' => $header,
		);
		
		$records = $this->getXmlFiles($nid);
		$xml_node = node_load($nid);
		$xml_title = $xml_node->getTitle();
		
		foreach ($records as $record) {
			$timeAgo = t('(created during the last @time months)', array('@time' => \Drupal::service('date.formatter')->formatTimeDiffSince($record->created_date)));
			// Resources
			$form['xml'][$record->id]['resource'] = array(
				'#plain_text' => 'Listing of ' . $xml_title . ' ' . $timeAgo,
			);
			
			// Type
			$form['xml'][$record->id]['type'] = array(
				'#plain_text' => isset($extensions[$record->type]) ? $extensions[$record->type] : $record->type,
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
				'#options' => [$record->file_id => ''],
				'#attributes' => ['xml_title' => 'Listing of ' . $xml_title],
				'#ajax' => [
					'callback' => '::xmlPreview',
					'wrapper' => 'xml-table-content',
					'progress' => [
						'type' => 'throbber',
						'message' => t('Loading Record...'),
					],
				],
			);
			
			if($showOperation) {
				$form['xml'][$record->id]['operations'] = array(
					'#type' => 'submit',
					'#value' => 'Delete',
					'#name' => 'delete_' . $record->id,
					'#id' => $record->id,
					'#submit' => ['::xmlDelete'],
				);
				
			}
			
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
			'#prefix' => '<div id="xml-table-content"><h4>'. $element['#attributes']['xml_title'] .'</h4><i>This is preview. If you would like to view the full resource, please download it above.</i>',
			'#suffix' => '</div>',
		];
		
		$form['xml_records']['tables'] = $tableContent;
		
		$form_state->setValues([]);
		$form_state->setRebuild(TRUE);
		
		return $form['xml_records'];
  }
	
	/**
	 *
	*/
	public function loadXmlRecordsTable($fid) {
		$file = File::load($fid);
		$type = $file->getMimeType();
		
    $filePath = file_create_url($file->getFileUri());
		
		$header = $rows = [];
		$item = 0;
		
		switch($type) {
			case 'application/xml':
				$xml = simplexml_load_file($filePath);
				foreach($xml->children() as $record) {
					$record = (array)$record;
					$header = array_keys($record);
					$rows[] = $record;
				}
				break;
			case 'text/csv':
				$file = fopen($filePath, "r");
				$header = fgetcsv($file);
				while($column = fgetcsv($file)) {
					$rows[] = array_combine(header);
				}
				fclose($file);
				break;
		}
		
		$form = [
			'#type' => 'table',
			'#header' => $header,
			'#footer' => [
        [
          'data' => $header,
        ]
      ],
			'#rows' => $rows,
      '#attributes' => [
        'id' => 'xml-preview-datatables', 
      ]
		];
		
		return $form;
	} 
	
	/**
	* {@inheritdoc}
	*/
	public function xmlDelete(array &$form, FormStateInterface $form_state) {
		$element = $form_state->getTriggeringElement();
		$id = $element['#id'];
		$this->deleteXmlFiles($id);
		$form_state->setRebuild(FALSE);
	}
	
  /**
  * Get XML Files
  */
  public function getXmlFiles($nid = '') {
    $connection = Database::getConnection();
    $query = $connection->select('santa_xml_upload', 'xml');
    $query->condition('xml.nid', $nid);
    $query->fields('xml');
    $results = $query->execute();
    return $results;
    
  }
  
  /**
  * Delete XML Files
  */
  public function deleteXmlFiles($id) {
    $connection = Database::getConnection();
    $query = $connection->delete('santa_xml_upload');
    $query->condition('id', $id);
    $results = $query->execute();
  }


}
