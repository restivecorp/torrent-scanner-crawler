<?php
  require_once('common.php');

  // All episodes from a serie
  function extract_all($serie, $list, $download) {
      $torrents = get_all_episodes($serie);

      // manage
      foreach ($torrents as $t) {
          wlog("   > $t");
      }

      if ($list == 1) {
          list_data($torrents);
      }

      if ($download == 1) {
          download_data($torrents);
      }
  }

  // Next episode from a serie
  function extract_next($serie, $episode, $continue, $list, $download) {
      $torrents = get_all_episodes($serie);

      // manage
      $torrentsData = compose_torrents_data($torrents);

      $torrentsDetected = Array();
      $lastDetected = "N/A";

      foreach (array_reverse($torrentsData) as $k => $v) {
        if (is_more_than($k, $episode)) {
          array_push($torrentsDetected, $v);
          $lastDetected = $k;

          if ($continue == 0) {
            break;
          } else {
            $episode = $k;
          }
        }
      }

      if (!empty($torrentsDetected)) {
        foreach ($torrentsDetected as $t) {
          wlog("   > $t");
        }

        if ($list == 1) {
            list_data($torrentsDetected);
        }

        if ($download == 1) {
            download_data($torrentsDetected);
        }
      } else {
          wlog("   > There is no next episode.");
      }

      return $lastDetected;
  }

  // Next episode from a serie, from active series in database
  function extract_batch() {
    $activeSeries = get_series_from_db();

    foreach ($activeSeries as $s) {
      wlog(" > " . $s['name'] . ": " . $s['lastEpisode'] . "." . "Is there a next episode?");
      $last = extract_next($s['search'], $s['lastEpisode'], 1, 0, 1);

      if ($last != "N/A") {
        update_next_episode($last, $s['id']);
      }
    }
  }

  /*
    Auxiliar | Util functions
    ---------------------------
  */

  // Extract all episodes from serie
  function get_all_episodes($serie) {
      // get html
      $htmlContent = make_http_request($serie);
      $domdoc = new DOMDocument();
      $domdoc->loadHTML($htmlContent);

      // parse
      $tds = $domdoc->getElementsByTagName('td');

      $torrents = array();
      for ($i = 0; $i < $tds->length; $i+=3) {
          array_push($torrents, $tds->item($i)->getElementsByTagName('a')->item(0)->attributes->getNamedItem('href')->nodeValue);
      }

      return $torrents;
  }

  // Create an associative array like NxMM => torrent_url
  function compose_torrents_data($torrents) {
      $episodes = array();

      $nxn = '/[[:digit:]]+x[[:digit:]][[:digit:]]+/';
      $nnn = '/_\d*_/';

      $matches = array();

      foreach ($torrents as $t) {
          $name = array_pop(explode('/', $t));

          // if contais NxMM format
          preg_match($nxn, $name, $matches);
          if (!empty($matches)) {
              $episodes[array_pop($matches)] = $t;
              $matches = array();
          }

          // if contais _NN_ format
          preg_match($nnn, $name, $matches);
          if (!empty($matches)) {
              $e = array_pop($matches);
              // delete _
              $e =  str_replace("_", "", $e);
              $episode = "";

              // convert into NxMM
              if (strlen($e) == 2) {
                  // nn --> nx0n
                  $episode = $e[0]."x".$e[1];
              }

              if (strlen($e) == 3) {
                  // nnn --> nxnn
                  $episode = $e[0]."x".$e[1].$e[2];
              }

              if (strlen($e) > 3) {
                  // nnnn --> nnxnn
                  $episode = $e[0].$e[1]."x".$e[2].$e[3];
              }

              $episodes[$episode] = $t;
              $matches = array();
          }
      }

      // review: must be return ordered 1, 2, 3, 4, ...
      return $episodes;
  }

  // compare 2 episodes to know if a > b
  function is_more_than($ea, $eb) {
    $a = explode("x", $ea);
    $b = explode("x", $eb);

    // same session
    if ($a[0] == $b[0]) {
      return $a[1] > $b[1];
    }

    // different seassion
    return $a[0] > $b[0];
  }


  // get all series from database
	function get_series_from_db() {
		$query = "select * from serie where (active = 1)";
		$db = new SQLite3(get_db_path());
		$results = $db->query($query);

		$data = array();
        while($row = $results->fetchArray(SQLITE3_ASSOC)){
          array_push($data, $row);
        }

		$db->close();
		return $data;
	}

  // Update seri with next episode
  function update_next_episode($episode, $serieId) {
    $last = "'".$episode."'";

    $db = new SQLite3(get_db_path());
    $db->exec("update serie set lastEpisode = ".$last." where id = ". $serieId);

    $db->close();
  }

  // Print results
  function list_data($data) {
      foreach ($data as $d) {
          print(" > " . $d . "\n");
      }
  }

  // Download torrent
  function download_data($data) {
      foreach ($data as $d) {
          $name = explode('/', $d);
          exec("curl -s '". $d . "' > " . get_download_output_directory() . "'" . array_pop($name) . "'");
      }
  }
