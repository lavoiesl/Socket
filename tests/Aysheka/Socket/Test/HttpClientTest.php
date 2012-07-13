<?php

namespace Aysheka\Socket\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class HttpClientTest extends TestBase {

  public $host   = 'localhost';
  public $port   = 80;
  protected $client = null;

  public function setUp($verbose=true) {
    parent::setUp($verbose);
    $this->client = $this->newClient();
  }

  public function testGET() {
    $this->client->connect();
    $this->client->send("GET / HTTP/1.0\r\n\r\n");
    $reply = $this->client->read();

    preg_match('/^HTTP\/1\.[0-1] ((\d+) (.+))$/m', $reply, $matches);
    // $this->assertGreaterThan(0, count($matches), "Couldn’t understand reply: $reply");
    // $this->assertEquals('200', $matches[2], "HTTP Server replied with {$matches[1]}");

    $this->client->close();
  }
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == __FILE__) {
  $test = new HttpClientTest;
  $test->testGET();
}
