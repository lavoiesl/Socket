<?php

namespace Aysheka\Socket\Test;

use Aysheka\Socket\Client;
use Aysheka\Socket\Socket;
use Aysheka\Socket\Server;
use Aysheka\Socket\DomainProtocol;
use Aysheka\Socket\SocketProtocol;
use Aysheka\Socket\SocketType;
use Aysheka\Socket\Event\SocketEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class TestBase {

  protected $eventDispatcher;
  public $host = 'localhost';
  public $port = 8089;

  public function setUp($verbose=true) {
    $this->eventDispatcher = new EventDispatcher;
    if ($verbose) $this->addEchoListeners();
  }

  public function getEvents() {
    return array(
      SocketEvent::OPEN,
      SocketEvent::CLOSE,
      SocketEvent::CONNECT,
      SocketEvent::BIND,
      SocketEvent::READ,
      SocketEvent::WRITE,
    );
  }

  protected function newClient() {
    return new Client(
      $this->host,
      $this->port,
      DomainProtocol::create(DomainProtocol::IP4),
      SocketType::create(SocketType::STREAM),
      SocketProtocol::create(SocketProtocol::TCP),
      $this->eventDispatcher
    );
  }
  
  protected function newServer() {
    return new Server(
      $this->host,
      $this->port,
      DomainProtocol::create(DomainProtocol::IP4),
      SocketType::create(SocketType::STREAM),
      SocketProtocol::create(SocketProtocol::TCP),
      $this->eventDispatcher
    );
  }

  protected function addEventListener($event_id, $callback) {
    $this->eventDispatcher->addListener($event_id, $callback);
  }

  protected function addEchoListeners() {
    array_map(array($this, 'addEchoListener'), $this->getEvents());
  }

  protected function addEchoListener($event_id) {
    $trimmed = static::trimEventId($event_id);
    $this->addEventListener($event_id, function(SocketEvent $event) use ($trimmed) {
      $data = $event->getData();
      $class = TestBase::trimClass(get_class($event->getSocket()));

      if (is_string($data) && !empty($data)) {
        $data = trim($data);
        if (strlen($data) > 96) $data = substr($data, 0, 96) . ' […]';
        echo "$class.$trimmed: '" . $data . "'\n";
      } else {
        echo "$class.$trimmed\n";
      }
    });
  }

  protected function addEchoDataListener($event_id) {
    $echo = static::trimEventId($event_id) . ": ";
    $this->addEventListener($event_id, function(SocketEvent $event) use ($echo) {
      echo $echo . $event->getData();
    });
  }

  protected static function trimEventId($event_id) {
    return str_replace(SocketEvent::BASE . '.', '', $event_id);
  }

  protected static function trimClass($class) {
    $parts = explode('\\', $class);
    return array_pop($parts);
  }

}
