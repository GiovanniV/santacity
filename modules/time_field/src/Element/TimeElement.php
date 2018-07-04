<?php

namespace Drupal\time_field\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\time_field\Time;

/**
 * Provides a one-line text field form element.
 *
 * Properties:
 * - #maxlength: Maximum number of characters of input allowed.
 * - #size: The size of the input element in characters.
 * - #autocomplete_route_name: A route to be used as callback URL by the
 *   autocomplete JavaScript library.
 * - #autocomplete_route_parameters: An array of parameters to be used in
 *   conjunction with the route name.
 *
 * Usage example:
 * @code
 * $form['title'] = array(
 *   '#type' => 'textfield',
 *   '#title' => $this->t('Subject'),
 *   '#default_value' => $node->title,
 *   '#size' => 60,
 *   '#maxlength' => 128,
 * '#required' => TRUE,
 * );
 * @endcode
 *
 * @see \Drupal\Core\Render\Element\Color
 * @see \Drupal\Core\Render\Element\Email
 * @see \Drupal\Core\Render\Element\MachineName
 * @see \Drupal\Core\Render\Element\Number
 * @see \Drupal\Core\Render\Element\Password
 * @see \Drupal\Core\Render\Element\PasswordConfirm
 * @see \Drupal\Core\Render\Element\Range
 * @see \Drupal\Core\Render\Element\Tel
 * @see \Drupal\Core\Render\Element\Url
 *
 * @FormElement("time")
 */
class TimeElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processAjaxForm'],
      ],
      '#pre_render' => [
        [$class, 'preRenderTime'],
      ],
      '#theme' => 'input__time',
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input !== FALSE && $input !== NULL) {
      $time = \Drupal\time_field\Time::createFromHtml5Format($input);
      return  $time->getTimestamp();
    }

    return NULL;
  }

  /**
   * Prepares a #type 'textfield' render element for input.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #size, #maxlength,
   *   #placeholder, #required, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  public static function preRenderTime($element) {
    $element['#attributes']['type'] = 'time';
    $element['#attributes']['class'] = ['form-time'];

    // todo: in ajax request value is set to raw timestamp, perform a better solution here
    $isValuePassedInTimestampFormat = preg_match('/^\d+$/', $element['#value']);
    if ($isValuePassedInTimestampFormat) {
      $element['#value'] = Time::createFromTimestamp($element['#value'])->formatForWidget();
    }

    Element::setAttributes($element, ['id', 'name', 'value', 'size', 'maxlength', 'placeholder']);
    static::setAttributes($element, ['form-text']);

    return $element;
  }
}
