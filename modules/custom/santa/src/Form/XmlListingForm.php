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
    $form['mytable'] = array(
			'#type' => 'table',
			'#header' => array(t('Resource'), t('Type'), t('Download'), t('Preview')),
		);
		
		$records = $this->getXmlFiles();
		dpm($records);
		foreach ($records as $record) {
			// Resources
			$form['mytable'][$record->id]['resource'] = array(
				'#plain_text' => $record->type,
			);
			
			// Type
			$form['mytable'][$record->id]['type'] = array(
				'#plain_text' => 'csv',
			);
			
			// Download
			$form['mytable'][$record->id]['download'] = array(
				'#type' => 'link',
				'#title' => $this->t('Download'),
				'#url' => Url::fromRoute('file_download.link', array('scheme' => 'public', 'fid' => $record->file_id)),
			);
			
			// Preview
			$form['mytable'][$record->id]['preview'] = array(
				'#type' => 'radios',
				'#options' => [$record->id => $record->id],
			);
		}
		
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
