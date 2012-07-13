<?php

namespace Aysheka\Socket\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Aysheka\Socket\Event\ServerEvent;
use Aysheka\Socket\Exception\IOException;

class EchoServerTest extends TestBase {

  protected $server = null;

  public function setUp($verbose=true) {
    parent::setUp($verbose);
    $this->server = $this->newServer();
  }

  public function testEcho() {
    $this->addEventListener(ServerEvent::NEW_REQUEST, function (ServerEvent $event) {
      $event->getServer()->stop();
      $socket = $event->getSocket();

      try {
        while (($message = $socket->read()) && trim($message) != 'QUIT') {
          $socket->write($message);
        }
      } catch (IOException $e) {}

      $socket->close();
    });

    $this->server->create();

  }
}

if (realpath($_SERVER['SCRIPT_FILENAME']) == __FILE__) {
  $test = new EchoServerTest;
  $test->testEcho();
}
