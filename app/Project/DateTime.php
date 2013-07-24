<?php
namespace Project;

class DateTime extends \DateTime {
  public function format($str) {
    $months = array(
      'January' => 'Января',
      'February' => 'Февраля',
      'March' => 'Марта',
      'April' => 'Апреля',
      'May' => 'Мая',
      'June' => 'Июня',
      'July' => 'Июля',
      'August' => 'Августа',
      'September' => 'Сентября',
      'October' => 'Октября',
      'November' => 'Ноября',
      'December' => 'Декабря'
    );

    $d = parent::format($str);
    $d = str_replace(
      array_keys($months),
      array_values($months),
      $d
    );

    return $d;
  }
}