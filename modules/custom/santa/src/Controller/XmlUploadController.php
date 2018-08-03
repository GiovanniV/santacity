<?php
namespace Drupal\santa\Controller;

use Drupal\Core\Controller\ControllerBase;
/**
 * Provides route responses for the Example module.
 */
class XmlUploadController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function content() {
  	$current_user = \Drupal::currentUser();
		$userRole = $current_user->getRoles();
		
		$xmlUploadForm = '';
		if(in_array('super_admin', $userRole) || in_array('administrator', $userRole)) {
			$xmlUploadForm = \Drupal::formBuilder()->getForm('Drupal\santa\Form\XmlUploadForm', []);
		}
		$xmlListingForm = \Drupal::formBuilder()->getForm('Drupal\santa\Form\xmlListingForm', []);
		
		return [
			'#theme' => 'xml_upload',
			'#xml_upload_form' => $xmlUploadForm
			'#xml_listing_form' => $xmlListingForm
		];
  }


}
