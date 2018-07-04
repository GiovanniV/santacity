<?php

namespace Drupal\time_field;

use Webmozart\Assert\Assert;

class Time {

  /**
   * @var int Number of hours
   */
  private $hour;

  /**
   * @var int Number of minutes
   */
  private $minute;

  /**
   * @var int Number of seconds
   */
  private $second;

  public function __construct($hour = 0, $minute = 0, $second = 0) {
    Assert::range($hour, 0, 23);
    Assert::range($minute, 0, 59);
    Assert::range($second, 0, 59);
    $this->hour = $hour;
    $this->minute = $minute;
    $this->second = $second;
  }

  /**
   * @return \DateTime
   */
  private static function baseDateTime(): \DateTime {
    return new \DateTime('2012-01-01 00:00:00');
  }

  /**
   * @return int Number of hours
   */
  public function getHour() {
    return $this->hour;
  }

  /**
   * @return int Number of seconds
   */
  public function getSecond() {
    return $this->second;
  }

  /**
   * @return int Number of minutes
   */
  public function getMinute() {
    return $this->minute;
  }

  /**
   * @return int Number of seconds passed through midnight
   */
  public function getTimestamp() {
    $value = $this->hour * 60 * 60;
    $value += $this->minute * 60;
    $value += $this->second;
    return $value;
  }

  /**
   * @param $timestamp Number of seconds passed through midnight
   *     must be between 0 and 86400
   *
   * @return \Drupal\time_field\Time
   */
  public static function createFromTimestamp($timestamp) {
    Assert::range($timestamp, 0, 86400);
    $time = self::baseDateTime();
    $time->setTimestamp($time->getTimestamp() + $timestamp);
    return new self($time->format('H'), $time->format('i'), $time->format('s'));
  }

  /**
   * @param $string time string eg `12:30:20` or `12:30`
   *
   * @return \Drupal\time_field\Time
   */
  public static function createFromHtml5Format($string) {
    if (!$string) {
      return new self();
    }
    $inputs = explode(':', $string);
    if (count($inputs) === 2) {
      $inputs[] = 0;
    }
    list ($hour, $minute, $seconds) = $inputs;
    return new self($hour, $minute, $seconds);
  }

  /**
   * @return string Formatted time eg `12:30 AM`
   */
  public function format() {
    $time = self::baseDateTime();
    $time->setTimestamp($time->getTimestamp() + $this->getTimestamp());
    return $time->format('h:i a');
  }

  /**
   * @return string Formatted time eg `23:12:00`
   */
  public function formatForWidget() {
    $time = self::baseDateTime();
    $time->setTimestamp($time->getTimestamp() + $this->getTimestamp());
    return $time->format('H:i:s');
  }

  public function on(\DateTime $dateTime) {
    $instance = new \DateTime();
    $instance->setTimestamp($dateTime->getTimestamp());
    $instance->setTime(0, 0, 0);
    $instance->setTimestamp($instance->getTimestamp() + $this->getTimestamp());
    return $instance;
  }
}