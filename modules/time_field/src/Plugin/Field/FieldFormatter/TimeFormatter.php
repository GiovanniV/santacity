<?php

namespace Drupal\time_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\time_field\Time;

/**
 * Plugin implementation of the 'time_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "time_formatter",
 *   label = @Translation("Time formatter"),
 *   field_types = {
 *     "time"
 *   }
 * )
 */
class TimeFormatter extends FormatterBase {

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    $time = Time::createFromTimestamp($item->value);
    return $time->format();
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }
}
