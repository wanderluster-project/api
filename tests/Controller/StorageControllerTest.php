<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataModel\Entity\EntityId;
use App\DataModel\Entity\EntityTypes;
use App\Security\JwtTokenUtilities;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageControllerTest extends WebTestCase
{
    public function testUploadExceptions(): void
    {
        $client = self::getClient('simpkevin@gmail.com');

        // error if missing 'file' param
        $client->request('POST', '/api/v1/storage');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        // error if multiple files
        $file1 = '/var/www/wanderluster/tests/Fixtures/Files/sample.jpg';
        $file2 = '/var/www/wanderluster/tests/Fixtures/Files/sample.jpg';
        $client->request('POST', '/api/v1/storage', [], ['file' => [$file1, $file2]]);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        // error if not a file
        $client->request('POST', '/api/v1/storage', ['file' => 'foo']);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testUploadJpegTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.jpg';
        $file = new UploadedFile($filename, 'sample.jpg');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $entityId = new EntityId($data['id']);
        $this->assertArrayHasKey('url', $data['data']);
        $this->assertEquals(EntityTypes::FILE_IMAGE_JPG, $entityId->getEntityType());
        $this->assertEquals('image/jpeg', $data['data']['mime_type']);
        $this->assertEquals(filesize($filename), $data['data']['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entityId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadPngTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.png';
        $file = new UploadedFile($filename, 'sample.png');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $entityId = new EntityId($data['id']);
        $this->assertArrayHasKey('url', $data['data']);
        $this->assertEquals(EntityTypes::FILE_IMAGE_PNG, $entityId->getEntityType());
        $this->assertEquals('image/png', $data['data']['mime_type']);
        $this->assertEquals(filesize($filename), $data['data']['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entityId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadGifTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.gif';
        $file = new UploadedFile($filename, 'sample.gif');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $entityId = new EntityId($data['id']);
        $this->assertArrayHasKey('url', $data['data']);
        $this->assertEquals(EntityTypes::FILE_IMAGE_GIF, $entityId->getEntityType());
        $this->assertEquals('image/gif', $data['data']['mime_type']);
        $this->assertEquals(filesize($filename), $data['data']['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entityId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadSvgTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.svg';
        $file = new UploadedFile($filename, 'sample.svg');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $entityId = new EntityId($data['id']);
        $this->assertArrayHasKey('url', $data['data']);
        $this->assertEquals(EntityTypes::FILE_IMAGE_SVG, $entityId->getEntityType());
        $this->assertEquals('image/svg+xml', $data['data']['mime_type']);
        $this->assertEquals(filesize($filename), $data['data']['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entityId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadPdfTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.pdf';
        $file = new UploadedFile($filename, 'sample.pdf');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $entityId = new EntityId($data['id']);
        $this->assertArrayHasKey('url', $data['data']);
        $this->assertEquals(EntityTypes::FILE_PDF, $entityId->getEntityType());
        $this->assertEquals('application/pdf', $data['data']['mime_type']);
        $this->assertEquals(filesize($filename), $data['data']['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entityId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @param string $username
     */
    protected static function getClient($username = null): KernelBrowser
    {
        $jwt = '';
        if ($username) {
            $tokenUtilities = new JwtTokenUtilities();
            $jwt = $tokenUtilities->generate($username);
        }

        return static::createClient([], ['HTTP_AUTHENTICATION' => 'Bearer: '.$jwt]);
    }
}
