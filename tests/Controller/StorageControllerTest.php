<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Sharding\EntityTypes;
use App\Sharding\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageControllerTest extends WebTestCase
{
    public function testUploadExceptions(): void
    {
        $client = static::createClient();

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
        $client = static::createClient();
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.jpg';
        $file = new UploadedFile($filename, 'sample.jpg');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $uuid = new Uuid($data['uuid']);
        $this->assertArrayHasKey('uuid', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertEquals(EntityTypes::FILE_IMAGE_JPG, $uuid->getEntityType());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('image/jpeg', $data['mime_type']);
        $this->assertEquals(filesize($filename), $data['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$uuid);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadPngTest(): void
    {
        $client = static::createClient();
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.png';
        $file = new UploadedFile($filename, 'sample.png');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $uuid = new Uuid($data['uuid']);
        $this->assertArrayHasKey('uuid', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertEquals(EntityTypes::FILE_IMAGE_PNG, $uuid->getEntityType());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('image/png', $data['mime_type']);
        $this->assertEquals(filesize($filename), $data['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$uuid);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadGifTest(): void
    {
        $client = static::createClient();
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.gif';
        $file = new UploadedFile($filename, 'sample.gif');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $uuid = new Uuid($data['uuid']);
        $this->assertArrayHasKey('uuid', $data);
        $this->assertArrayHasKey('url', $data);
        var_dump($data);exit;
        $this->assertEquals(EntityTypes::FILE_IMAGE_GIF, $uuid->getEntityType());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('image/gif', $data['mime_type']);
        $this->assertEquals(filesize($filename), $data['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$uuid);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadSvgTest(): void
    {
        $client = static::createClient();
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.svg';
        $file = new UploadedFile($filename, 'sample.svg');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $uuid = new Uuid($data['uuid']);
        $this->assertArrayHasKey('uuid', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertEquals(EntityTypes::FILE_IMAGE_SVG, $uuid->getEntityType());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('image/svg+xml', $data['mime_type']);
        $this->assertEquals(filesize($filename), $data['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$uuid);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadPdfTest(): void
    {
        $client = static::createClient();
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.pdf';
        $file = new UploadedFile($filename, 'sample.pdf');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $uuid = new Uuid($data['uuid']);
        $this->assertArrayHasKey('uuid', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertEquals(EntityTypes::FILE_PDF, $uuid->getEntityType());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('application/pdf', $data['mime_type']);
        $this->assertEquals(filesize($filename), $data['file_size']);

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$uuid);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
