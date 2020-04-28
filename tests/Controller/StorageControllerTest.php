<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\StorageController;
use App\DataModel\Entity\EntityId;
use App\DataModel\Entity\EntityTypes;
use App\EntityManager\EntityManager;
use App\FileStorage\FileAdapters\GenericFileAdapter;
use App\Tests\FunctionalTest;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StorageControllerTest extends FunctionalTest
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

    public function testUploadServerError(): void
    {
        // mock error encountered when committing
        $sut = new StorageController();
        $fileAdapter = $this->getFileAdapter();
        $serializer = $this->getSerializer();
        $mockEntityManager = \Mockery::mock(EntityManager::class);
        $mockEntityManager->shouldReceive('commit')->andThrow(new Exception());
        $file = new UploadedFile('/var/www/wanderluster/tests/Fixtures/Files/sample.jpg', 'sample.jpg', 'image/jpeg', UPLOAD_ERR_OK, true);

        try {
            $sut->uploadFile(new Request([], [], [], [], ['file' => $file]), $fileAdapter, $serializer, $mockEntityManager);
            $this->fail('Exception not thrown');
        } catch (HttpException $e) {
            $this->assertInstanceOf(HttpException::class, $e);
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function testDeleteExceptions(): void
    {
        // 404 error should be thrown if issuing DEL to endpoint without entity id
        $client = self::getClient('simpkevin@gmail.com');
        $client->request('DELETE', '/api/v1/storage/');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        // 400 error if issuing DEL with invalid entity id
        $client->request('DELETE', '/api/v1/storage/I-AM-INVALID');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        // 404 error should be thrown if issuing DEL to non-existent entity id
        $client->request('DELETE', '/api/v1/storage/1-1000-0000000000000000');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteServerError(): void
    {
        // mock error encountered deleting file
        $entityId = '1-1000-0000000000000000';
        $sut = new StorageController();
        $mockFileAdapter = \Mockery::mock(GenericFileAdapter::class);
        $mockFileAdapter->shouldReceive('deleteRemoteFile')->andThrow(new Exception());

        try {
            $sut->deleteFile($entityId, new Request(), $mockFileAdapter);
            $this->fail('Exception not thrown');
        } catch (HttpException $e) {
            $this->assertInstanceOf(HttpException::class, $e);
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
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

    public function testGetFile(): void
    {
        $entityId = '1-1000-0000000000000000';
        $client = self::getClient('simpkevin@gmail.com');
        $client->request('POST', '/api/v1/storage/'.$entityId);
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }
}
