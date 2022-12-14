<?php
namespace Drupal\largefiles\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides route responses for the Example module.
 */
class LargeFilesController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function listPage() {
    // Grab settings
    $config = \Drupal::config('largefiles.settings');
    $filesizeCheck = $config->get('file_size');
    // If not set yet, use 2mb as default
    if ($filesizeCheck === '') {
      $filesizeCheck = 2;
    }
    // Convert to bytes
    $filesizeBytes = $filesizeCheck * 1024 * 1024;
    $database = \Drupal::database();
    
    $query = $database->query("SELECT fid, filename, filesize, filemime, uri FROM {file_managed} where filesize >= :filesize order by filesize desc", [
      ':filesize' => $filesizeBytes,
    ]);
    $result = $query->fetchAll();
    $output = '<thead><tr><th>File</th><th>Filesize</th><th>Type</th><th>Usage</th></tr><thead>';
    $output .= '<tbody>';

    // Go through each result and create row
    foreach ($result as $record) {
      $filesizeMB = round($record->filesize / 1024 / 1024,2);
      $output .= '<tr>';
      $output .= '<td><a href=". ' . file_create_url($record->uri) . '">' . $record->filename . '</a></td>';
      $output .= '<td>' . $filesizeMB . ' MB</td>';
      $output .= '<td>' . $record->filemime . '</td>';
      $output .= '<td><a href="/admin/content/files/usage/' . $record->fid . '">Usage</a></td>';
      
      
      $output .= '</tr>';
    }
    $output .= '</tbody>';

    // Display on page
    return [
      '#prefix' => '<h2>Files bigger than ' .  $filesizeCheck . ' MB</h2><table>',
      '#markup' => $output,
      '#suffix' => '</table>',
    ];
  }

}