<?php

namespace src\Routing;

use src\Routing\IRequest;

class Request implements IRequest
{
  function __construct()
  {
    $this->bootstrapSelf();
  }

  private function bootstrapSelf()
  {
    foreach ($_SERVER as $key => $value) {
      $this->{$this->toCamelCase($key)} = $value;
    }
  }

  private function toCamelCase($string)
  {
    $result = strtolower($string);

    preg_match_all('/_[a-z]/', $result, $matches);

    foreach ($matches[0] as $match) {
      $c = str_replace('_', '', strtoupper($match));
      $result = str_replace($match, $c, $result);
    }

    return $result;
  }

  public function getBody()
  {
    if ($this->requestMethod === "GET") {
      return;
    }


    if ($this->requestMethod == "POST") {

      $content = trim(file_get_contents("php://input"));
      $data = json_decode($content, true);
      $body = array();
      return $data;
    }
  }
}
