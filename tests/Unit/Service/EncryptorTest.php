<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Exception\EncryptorException;
use Spipu\CoreBundle\Service\Encryptor;
use Spipu\CoreBundle\Service\EncryptorInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(Encryptor::class)]
class EncryptorTest extends TestCase
{
    public function testService(): void
    {
        $service = new Encryptor('');
        $this->assertInstanceOf(EncryptorInterface::class, $service);
    }

    public function testServiceOk(): void
    {
        $keyPair = (new Encryptor(''))->generateKeyPair();
        $service = new Encryptor($keyPair);

        $originalString = 'My string to encode';

        $encryptedString = $service->encrypt($originalString);
        $decryptedString = $service->decrypt($encryptedString);

        $this->assertNotEquals($originalString, $encryptedString);
        $this->assertSame($originalString, $decryptedString);
    }

    public function testServiceKoEncryptEmptyKeyPair(): void
    {
        $keyPair = '';
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->encrypt('my string');
    }

    public function testServiceKoEncryptWrongKeyPairBadBase64(): void
    {
        $keyPair = 'wrong_key_pair';
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->encrypt('my string');
    }

    public function testServiceKoEncryptWrongKeyPairBadFormat(): void
    {
        $keyPair = sodium_bin2base64('wrong_key_pair', SODIUM_BASE64_VARIANT_ORIGINAL);
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->encrypt('my string');
    }

    public function testServiceKoDecryptWrongKeyPairBadFormat(): void
    {
        $keyPair = sodium_bin2base64('wrong_key_pair', SODIUM_BASE64_VARIANT_ORIGINAL);
        $service = new Encryptor($keyPair);

        $this->expectException(EncryptorException::class);
        $service->decrypt('my string');
    }

    public function testServiceDecryptReturnsNullWithWrongKeyPair(): void
    {
        $keyPairA = (new Encryptor(''))->generateKeyPair();
        $keyPairB = (new Encryptor(''))->generateKeyPair();

        $serviceA = new Encryptor($keyPairA);
        $serviceB = new Encryptor($keyPairB);

        $encrypted = $serviceA->encrypt('my secret');
        $result = $serviceB->decrypt($encrypted);

        $this->assertNull($result);
    }
}
