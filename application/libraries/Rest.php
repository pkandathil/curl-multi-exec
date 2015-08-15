<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Rest {

  /**
  * Single curl get request.
  * @param string $url: Url to make the get request on
  * @return string Response from the url
  */
  public function make_curl_request($url){
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);
    return $output;
  }

  /**
  * Make multiple curl GET requests.
  * @param array<string> The urls to request on
  * @param array<string, function> $callbacks. A hashmap of url to callback function
  * @return array<string> The content returned by all the urls
  */
  public function curl_multi_request($urls, $callbacks = []) {
      $ch = array();
      $results = array();
      $mh = curl_multi_init();
      foreach($urls as $key => $val) {
          $ch[$key] = curl_init();
          curl_setopt($ch[$key], CURLOPT_URL, $val);
          curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
          curl_multi_add_handle($mh, $ch[$key]);
      }
      $running = null;

      do {
          curl_multi_exec($mh, $running);
          $info = curl_multi_info_read($mh);
          if (false !== $info) {
            $curl_info = curl_getinfo($info['handle']);
            if ($curl_info['http_code'] == 200)  {
              $output = curl_multi_getcontent($info['handle']);
              $url = $curl_info['url'];
              $results[$curl_info['url']] = $output;

              if(isset($callbacks[$url])){
                $callbacks[$url]($url, $output);
              }
            }
          }
      }
      while ($running > 0);

      //remove handles.
      foreach ($ch as $key => $val) {
          curl_multi_remove_handle($mh, $val);
      }
      curl_multi_close($mh);
      return $results;
  }

}