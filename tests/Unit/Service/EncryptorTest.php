<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Exception\EncryptorException;
use Spipu\CoreBundle\Service\Encryptor;
use Spipu\CoreBundle\Service\EncryptorInterface;

class EncryptorTest extends TestCase
{
    public function testService()
    {
        $service = new Encryptor('');
        $this->assertInstanceOf(EncryptorInterface::class, $service);
    }

    public function testServiceOk()
    {
        $keyPair = (new Encryptor(''))->generateKeyPair();
        $service = new Encryptor($keyPair);

        $originalString = 'My string to encode';

        $encryptedString = $service->encrypt($originalString);
        $decryptedString = $service->decrypt($encryptedString);

        $this->assertNotEquals($originalString, $encryptedString);
        $this->assertSame($originalString, $decryptedString);
    }

    public function testServiceKoEncryptEmptyKeyPair()
    {
        $keyPair = '';
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->encrypt('my string');
    }

    public function testServiceKoEncryptWrongKeyPairBadBase64()
    {
        $keyPair = 'wrong_key_pair';
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->encrypt('my string');
    }

    public function testServiceKoEncryptWrongKeyPairBadFormat()
    {
        $keyPair = sodium_bin2base64('wrong_key_pair', SODIUM_BASE64_VARIANT_ORIGINAL);
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->encrypt('my string');
    }

    public function testServiceKoDecryptWrongKeyPairBadFormat()
    {
        $keyPair = sodium_bin2base64('wrong_key_pair', SODIUM_BASE64_VARIANT_ORIGINAL);
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->decrypt('my string');
    }
}
