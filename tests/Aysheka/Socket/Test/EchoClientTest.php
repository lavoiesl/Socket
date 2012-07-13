<?php

namespace Aysheka\Socket\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class EchoClientTest extends TestBase {

  protected $client = null;

  public function setUp($verbose=true) {
    parent::setUp($verbose);
    $this->client = $this->newClient();
  }

  public function testEcho() {
    $this->client->connect();

    $send = 'HELO';
    $this->client->send($send);
    $reply = $this->client->read();

    $this->client->send('QUIT');

    $this->client->close();
  }
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == __FILE__) {
  $test = new EchoClientTest;
  $test->testEcho();
}
