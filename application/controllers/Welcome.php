<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
    $this->benchmark->mark('code_start');
    echo "<pre>"; echo $this->make_curl_request("http://localhost:10012/welcome/foo");
    flush();
    echo "<pre>"; echo $this->make_curl_request("http://localhost:10012/welcome/foo");
    $this->benchmark->mark('code_end');
    echo "<pre>"; echo $this->benchmark->elapsed_time('code_start', 'code_end');
	}

  public function index_async(){
    $this->benchmark->mark('code_start');
    $result = $this->curl_multi_request(["http://localhost:10012/welcome/foo","http://localhost:10012/welcome/foo"]);
    $this->benchmark->mark('code_end');
    echo "<pre>"; var_dump($result);
    echo $this->benchmark->elapsed_time('code_start', 'code_end');
  }

  public function foo(){
    sleep(2);
    echo "asdf";
    header("HTTP/1.1 200 OK");
  }

  protected function make_curl_request($url){
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



protected function curl_multi_request($urls, $options = array()) {
    $ch = array();
    $results = array();
    $mh = curl_multi_init();
    foreach($urls as $key => $val) {
        $ch[$key] = curl_init();
        if ($options) {
            curl_setopt_array($ch[$key], $options);
        }
        curl_setopt($ch[$key], CURLOPT_URL, $val);
        curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($mh, $ch[$key]);
    }
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    }
    while ($running > 0);
    // Get content and remove handles.
    foreach ($ch as $key => $val) {
        $results[$key] = curl_multi_getcontent($val);
        curl_multi_remove_handle($mh, $val);
    }
    curl_multi_close($mh);
    return $results;
}

}
