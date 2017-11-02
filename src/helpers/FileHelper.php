<?php

namespace pkpudev\components\helpers;

use yii\helpers\FileHelper as YiiFileHelper;
use yii\web\UploadedFile;

/**
 * Formatter for Number and Numeric
 */
class FileHelper extends YiiFileHelper
{
	/**
	 * @param integer $number Number to be formatted
	 * @return string Readable format
	 */
	public static function formatReadable($number)
  {
    $sizeSuffixes = ['B', 'KB', 'MB', 'GB'];
    if ($number == 0) return "0 {$sizeSuffixes[0]}";

    $place = (int)floor(log($number, 1024));
    $num   = round($number / pow(1024, $place), 2);
    return "{$num} $sizeSuffixes[$place]";
  }

  /**
   * @param string $ext Extension to favicon
   * @return string favicon class
   */
  public static function getFaIcon($ext)
  {
    $ext = strtolower($ext);

    if (in_array($ext, ['csv', 'xls', 'xlsx'])) 				return 'fa fa-file-excel-o';
    if (in_array($ext, ['doc', 'docx']))                return 'fa fa-file-word-o';
    if (in_array($ext, ['gif', 'jpeg', 'jpg', 'png']))  return 'fa fa-file-image-o';
    if (in_array($ext, ['pdf']))                        return 'fa fa-file-pdf-o';
    if (in_array($ext, ['ppt','pptx']))                 return 'fa fa-file-powerpoint-o';
    if (in_array($ext, ['rtf','txt']))                  return 'fa fa-file-text-o';
    if (in_array($ext, ['zip','rar']))                  return 'fa fa-file-archive-o';
    if (in_array($ext, ['folde']))                      return 'fa fa-folder-o';

    return 'fa fa-file-o';
  }
}