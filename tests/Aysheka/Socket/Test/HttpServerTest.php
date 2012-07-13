<?php

namespace Aysheka\Socket\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Aysheka\Socket\Event\ServerEvent;

class HttpServerTest extends TestBase {

  protected $server = null;

  public function setUp($verbose=true) {
    parent::setUp($verbose);
    $this->server = $this->newServer();
  }

  public function testGET() {
    $this->addEventListener(ServerEvent::NEW_REQUEST, function (ServerEvent $event) {
      $event->getServer()->stop();
      $socket = $event->getSocket();

      $request = $socket->read();
      preg_match('/^(?<method>[A-Z]+) (?<url>.+?)( HTTP\/(?<version>1\.[0-1]))?/m', $request, $matches);

      // $this->assertGreaterThan(0, count($matches), "Couldn’t understand request: $request");
      // $this->assertEquals('GET', $matches['method']);
      $version = empty($matches['version']) ? '1.0' : $matches['version'];

      $reply = <<<HTTP
HTTP/$version 200 OK
Connection: close
Content-Type: text/plain

Test
HTTP;
      $socket->write($reply);

      $socket->close();
    });

    $this->server->create();

  }
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == __FILE__) {
  $test = new HttpServerTest;
  $test->testGET();
}
