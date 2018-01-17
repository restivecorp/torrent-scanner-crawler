<?php
  require_once('common.php');
  require_once('crawler.php');

  // Script access point
  if (isset($argv)) {
      main($argv);
  }


  // Main method
  function main($argv) {
      if (!isset($argv[1])) {
          help();
          return;
      }

      // all
      if ($argv[1] == "-all" && isset($argv[2])) {
          $l = 0;
          $d = 0;

          if (in_array("-l", $argv)) {
              $l = 1;
          }

          if (in_array("-d", $argv)) {
              $d = 1;
          }

          if (!filter_var($argv[2], FILTER_VALIDATE_URL)) {
              print("Invalid URL: $argv[2] \n");
              return;
          }

          crawler_all($argv[2], $l, $d);
          return;
      }

      // next
      if ($argv[1] == "-next" && isset($argv[2]) && isset($argv[3])) {
          $c = 0;
          $l = 0;
          $d = 0;

          if (in_array("-c", $argv)) {
              $c = 1;
          }

          if (in_array("-l", $argv)) {
              $l = 1;
          }

          if (in_array("-d", $argv)) {
              $d = 1;
          }

          if (!filter_var($argv[2], FILTER_VALIDATE_URL)) {
              print("Invalid URL: $argv[2] \n");
              return;
          }

          crawler_next($argv[2], $argv[3], $c, $l, $d);
          return;
      }

      // batch
      if ($argv[1] == "-batch") {
          crawler_batch();
          return;
      }

      help();
  }


  // Show command help
  function help() {
      print("\nSeries Torrent Scanner:\n");
      print("-----------------------\n");

      print("$ php scanner.php OPTION {PARAMETERS} [ACTIONS] \n");

      print("\n - OPTIONS:\n");
      print("\t-all {URL} [-d][-l]              : Search all episodes\n");
      print("\t-next {URL} {LAST} [-c][-d] [-l] : Search next episode\n");
      print("\t-batch                           : Search next episode from active series\n\n");

      print("\n - PARAMETERS:\n");
      print("\tURL  : URL from web page torrent\n");
      print("\tLAST : Name of last episode (NxMM)\n");

      print("\n - ACTIONS:\n");
      print("\t-c : Continue whith next\n");
      print("\t-d : Download\n");
      print("\t-l : List\n\n");
  }

  function crawler_all($serie, $l, $d) {
      wlog("crawler_all (start): $serie -l: $l -d: $d");

      set_error_handler(function () { /* ignore errors */
      });
      extract_all($serie, $l, $d);
      restore_error_handler();

      wlog("crawler_all (end)  : $serie -l: $l -d: $d\n");
  }

  function crawler_next($serie, $episode, $c, $l, $d) {
      wlog("crawler_next (start): $serie $episode -c: $c -l: $l -d: $d");

      set_error_handler(function () { /* ignore errors */
      });
      extract_next($serie, $episode, $c, $l, $d);
      restore_error_handler();

      wlog("crawler_next (end)  : $serie $episode -c: $c -l: $l -d: $d\n");
  }

  function crawler_batch() {
      wlog("crawler_batch (start)");

      set_error_handler(function () { /* ignore errors */
      });
      extract_batch();
      restore_error_handler();

      wlog("crawler_batch (end)\n");
  }
