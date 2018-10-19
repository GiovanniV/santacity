<?php

namespace Drupal\santa\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class XmlConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'santa_xml_configuration';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'santa.xml_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('santa.xml_settings');
    $form['content_types'] = [
      '#type' => 'select',
      '#multiple' => true,
      '#options' => $this->getContentTypes(),
      '#title' => $this->t('Content Type'),
      '#default_value' => $config->get('content_types'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('content_types');
    $this->config('santa.xml_settings')
      ->set('content_types', $values)
      ->save();
    parent::submitForm($form, $form_state);
  }
	
	/**
	 *
	 */
	public function getContentTypes() {
		$contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
		
		$contentTypesList = [];
		foreach ($contentTypes as $contentType) {
			$contentTypesList[$contentType->id()] = $contentType->label();
		}

		return $contentTypesList;
	}		
}