<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Encryptor;
use Spipu\CoreBundle\Service\EncryptorInterface;

class EncryptorTest extends TestCase
{
    public function testService()
    {
        $keyPair = (new Encryptor(''))->generateKeyPair();

        $service = new Encryptor($keyPair);
        $this->assertInstanceOf(EncryptorInterface::class, $service);

        $originalString = 'My string to encode';

        $encryptedString = $service->encrypt($originalString);
        $decryptedString = $service->decrypt($encryptedString);

        $this->assertNotEquals($originalString, $encryptedString);
        $this->assertSame($originalString, $decryptedString);
    }
}
