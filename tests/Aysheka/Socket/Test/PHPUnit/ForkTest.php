<?php
namespace Aysheka\Socket\Test\PHPUnit;

use Aysheka\Socket\Test;
use PHPUnit_Framework_TestCase;
use Closure;

class EchoForkTest extends PHPUnit_Framework_TestCase {

  public function setUp($verbose=true) {
    // Do not extend
    $this->assertTrue(extension_loaded('pcntl'), "Pcntl is need for this test");
  }

  protected function runForked(Closure $parent, Closure $child) {
    $pid = pcntl_fork();

    $this->assertNotEquals($pid, -1, 'Could not fork');
    
    if ($pid) {
      $parent();

      pcntl_wait($status); //Protect against Zombie children
    } else {
      usleep(400 * 1000); // 400 ms

      $child();
    }
  }

  public function testHttp() {
    $this->runForked(
      function() {
        $test = new Test\HttpServerTest();
        $test->port = 8089;
        $test->setUp(false);
        $test->testGET();
      },
      function() {
        $test = new Test\HttpClientTest();
        $test->port = 8089;
        $test->setUp(false);
        $test->testGET();
      }
    );
  }

  public function testEcho() {
    return;
    $this->runForked(
      function() {
        $test = new Test\EchoServerTest();
        $test->port = 8090;
        $test->setUp(false);
        $test->testEcho();
      },
      function() {
        $test = new Test\EchoClientTest();
        $test->port = 8090;
        $test->setUp(false);
        $test->testEcho();
      }
    );
  }
}
