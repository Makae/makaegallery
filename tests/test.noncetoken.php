<?php
require_once('../loader.php');

load_test_dependencies('../');

use ch\makae\makaegallery\NonceToken;
use PHPUnit\Framework\TestCase;

class NonceTokenTest extends TestCase
{
    public function test_unserializingNonceToken_works()
    {
        $nonceToken = new NonceToken("myId", new DateTime("2020-10-31T13:15:22Z"));
        $serializedToken = serialize($nonceToken);
        $unserializedToken = unserialize($serializedToken);

        $this->assertEquals($nonceToken->getId(), $unserializedToken->getID());
        $this->assertEquals($nonceToken->getValidUntil(), $unserializedToken->getValidUntil());
    }

}
