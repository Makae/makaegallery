<?php
require_once('../loader.php');

load_dependencies('../');
require_once('mocks/mock.sessionprovider.php');

use ch\makae\makaegallery\SessionProviderMock;
use PHPUnit\Framework\TestCase;

class SessionProviderMockTest extends TestCase
{

    public function test_set_newValue_shouldSetNewValue()
    {
        $mock = new SessionProviderMock();
        $mock->set("key", "value");
        $this->assertEquals("value", $mock->get("key"));
    }

    public function test_set_existingValue_shouldSetNewValue()
    {
        $mock = new SessionProviderMock();
        $mock->set("key", "value");
        $mock->set("key", "value2");
        $this->assertEquals("value2", $mock->get("key"));
    }

    public
    function test_remove_existingValue_shouldRemoveValueCompletely()
    {
        $mock = new SessionProviderMock();
        $mock->set("key", "value");
        $mock->remove("key");
        $this->assertTrue(true);
    }

    public
    function test_remove_inexistentValue_shouldRemoveValueCompletely()
    {
        $mock = new SessionProviderMock();
        $mock->remove("key");
        $this->assertTrue(true);
    }

    public
    function test_has_existentValue_shouldReturnTrue()
    {
        $mock = new SessionProviderMock();
        $mock->set('test', 'value');
        $this->assertTrue($mock->has('test'));
    }

    public
    function test_has_inexistentValue_shouldReturnFalse()
    {
        $mock = new SessionProviderMock();
        $this->assertFalse($mock->has('test'));
    }
}

?>
