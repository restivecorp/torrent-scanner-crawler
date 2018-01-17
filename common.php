<?php
  /*
    CONFIGURATION VARIALEBLES | PATHS | ...
  */

  // Log directory
  function get_log_path() {
      return "./logs/";
  }

  // Download output directory
  function get_download_output_directory(){
      return "./dwn/";
  }

  // DB directory
  function get_db_path() {
      return "./db/torrents.db";
  }

  /*
    HTTP Requests
  */
  function make_http_request($url, $curl = 1) {
      sleep(1); // sleep 1 seconds to evict exceded limit int the page

      if ($curl == 1) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $data = curl_exec($ch);
          curl_close($ch);
          return $data;
      } else {
          // return file_get_contents($url);
      }
  }

  /*
    Log registry
  */
  function wlog($info) {
      $file = get_log_path()."scanner_".date("Y-m-d").".log";

      $log = fopen($file, "a+");

      if ($info == "") {
          fwrite($log, "\n");
      } else {
          fwrite($log, "[".date("Y-m-d H:i:s")."]: $info\n");
      }

      fclose($log);
      chmod($file, 0777);
  }
?>
