<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Welcome extends CI_Controller {

  protected $callback_results = [];

  /**
  * Function makes service requests sequentially. Processing the output at the end of each service call
  */
  public function index()
  {
    echo "<pre>Function makes service requests sequentially. Processing the output at the end of each service call";
    $this->load->library('Rest');
    $this->benchmark->mark('code_start');
    $url = "http://localhost:10012/welcome/foo";
    $result = $this->rest->make_curl_request($url);
    $result = $this->process_callback_response($url, $result);
    echo "<pre>"; var_dump($result);

    $url = "http://localhost:10012/welcome/foo_fast";
    $result = $this->rest->make_curl_request($url);
    $result = $this->process_callback_response($url, $result);
    echo "<pre>"; var_dump($result);
    $this->benchmark->mark('code_end');
    echo "<pre>"; echo $this->benchmark->elapsed_time('code_start', 'code_end');
  }

  /**
  * Function uses curl_multi to make a service request. Service requests are done parallel. Once all service responses are received. They are processed sequentially.
  */
  public function index_async(){
    echo "<pre> Function uses curl_multi to make a service request. Service requests are done parallel. Once all service responses are received. They are processed sequentially.";
    $this->load->library('Rest');
    $this->benchmark->mark('code_start');
    $results = $this->rest->curl_multi_request(["http://localhost:10012/welcome/foo","http://localhost:10012/welcome/foo_fast"]);
    $this->benchmark->mark('code_end');
    echo "<pre>";
    echo $this->benchmark->elapsed_time('code_start', 'code_end');
    foreach($results as $url => $service_response){
      echo "<pre>";
      echo $this->process_callback_response($url, $service_response);
    }
  }

  /**
  * Function uses curl_multi to make a service request. Service requests are done parallel. As the services respond, they are processed with callbacks.
  */
  public function index_async_callbacks(){
    echo "<pre> Function uses curl_multi to make a service request. Service requests are done parallel. As the services respond, they are processed with callbacks.";
    $this->load->library('Rest');
    $callbacks = [];
    $urls = ["http://localhost:10012/welcome/foo","http://localhost:10012/welcome/foo_fast"];
    foreach($urls as $url){
      $callbacks[$url] = function($url, $response_body){
        $this->callbacl_results[] = $this->process_callback_response($url, $response_body);
      };
    }
    $this->benchmark->mark('code_start');
    $results = $this->rest->curl_multi_request($urls, $callbacks);
    $this->benchmark->mark('code_end');
    echo "<pre>";
    echo $this->benchmark->elapsed_time('code_start', 'code_end');
    echo "<pre>"; var_dump($this->callbacl_results); die;
  }

  public function foo(){
    sleep(2);
    $this->generate_output("foo");
  }

  public function foo_fast(){
    sleep(1);
    $this->generate_output("foo fast");
  }

  /**
  * Generates random output for a function
  * @param string $function_name: Label used in the random output
  * @return void
  */
  protected function generate_output($function_name){
    $max_value = rand(1, 10000);
    for($i = 0; $i < $max_value; $i++){
      echo "\r\n" . $function_name . " " . $i ;
    }
  }

  /**
  * Process the service response. Not real error handling here. Just a test function
  * @param string $url The url for which to process the service response
  * @param string $response_body The body of the response
  * @return string|void the processes body.
  */
  protected function process_callback_response($url, $response_body){
    $response_body = explode("\n", $response_body);
    $last_line = $response_body[count($response_body) - 1];
    $final_output = $url . ": " . $last_line;
    return $final_output;
  }


}
