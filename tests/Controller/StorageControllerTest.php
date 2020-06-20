<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\StorageController;
use App\DataModel\Attributes\Attributes;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\EntityManager;
use App\DataModel\Translation\LanguageCodes;
use App\FileStorage\FileAdapters\ChainFileAdapter;
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

        // @todo Implement
//        // 404 error should be thrown if issuing DEL to non-existent entity id
//        $client->request('DELETE', '/api/v1/storage/0877bb25-8bf7-4b0b-926a-8c416f3a2624');
//        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

//    public function testDeleteServerError(): void
//    {
    // @todo Implement
//        // mock error encountered deleting file
//        $entityId = '0877bb25-8bf7-4b0b-926a-8c416f3a2624';
//        $sut = new StorageController();
//        $mockFileAdapter = \Mockery::mock(ChainFileAdapter::class);
//        $mockFileAdapter->shouldReceive('deleteRemoteFile')->andThrow(new Exception());
//
//        try {
//            $sut->deleteFile($entityId, new Request(), $mockFileAdapter);
//            $this->fail('Exception not thrown');
//        } catch (HttpException $e) {
//            $this->assertInstanceOf(HttpException::class, $e);
//            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
//        }
//    }

    public function testUploadJpegTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.jpg';
        $file = new UploadedFile($filename, 'sample.jpg');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entity = $this->getSerializer()->decode($client->getResponse()->getContent());
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertTrue($entity->has(Attributes::CORE_FILE_URL));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_SIZE));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals('image/jpeg', $entity->get(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals(EntityTypes::FILE_IMAGE_JPG, $entity->getEntityType());
        $this->assertNotNull($entity->getEntityId());

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entity->getEntityId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadPngTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.png';
        $file = new UploadedFile($filename, 'sample.png');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entity = $this->getSerializer()->decode($client->getResponse()->getContent());
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertTrue($entity->has(Attributes::CORE_FILE_URL));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_SIZE));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals('image/png', $entity->get(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals(EntityTypes::FILE_IMAGE_PNG, $entity->getEntityType());
        $this->assertNotNull($entity->getEntityId());

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entity->getEntityId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadGifTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.gif';
        $file = new UploadedFile($filename, 'sample.gif');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entity = $this->getSerializer()->decode($client->getResponse()->getContent());
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertTrue($entity->has(Attributes::CORE_FILE_URL));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_SIZE));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals('image/gif', $entity->get(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals(EntityTypes::FILE_IMAGE_GIF, $entity->getEntityType());
        $this->assertNotNull($entity->getEntityId());

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entity->getEntityId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadWebpTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.webp';
        $file = new UploadedFile($filename, 'sample.webp');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entity = $this->getSerializer()->decode($client->getResponse()->getContent());
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertTrue($entity->has(Attributes::CORE_FILE_URL));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_SIZE));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals('image/webp', $entity->get(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals(EntityTypes::FILE_IMAGE_WEBP, $entity->getEntityType());
        $this->assertNotNull($entity->getEntityId());

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entity->getEntityId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadSvgTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.svg';
        $file = new UploadedFile($filename, 'sample.svg');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entity = $this->getSerializer()->decode($client->getResponse()->getContent());
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertTrue($entity->has(Attributes::CORE_FILE_URL));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_SIZE));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals('image/svg+xml', $entity->get(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals(EntityTypes::FILE_IMAGE_SVG, $entity->getEntityType());
        $this->assertNotNull($entity->getEntityId());

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entity->getEntityId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadPdfTest(): void
    {
        $client = self::getClient('simpkevin@gmail.com');
        $filename = '/var/www/wanderluster/tests/Fixtures/Files/sample.pdf';
        $file = new UploadedFile($filename, 'sample.pdf');
        $client->request('POST', '/api/v1/storage', [], ['file' => $file]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entity = $this->getSerializer()->decode($client->getResponse()->getContent());
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertTrue($entity->has(Attributes::CORE_FILE_URL));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_SIZE));
        $this->assertTrue($entity->has(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals('application/pdf', $entity->get(Attributes::CORE_FILE_MIME_TYPE));
        $this->assertEquals(EntityTypes::FILE_PDF, $entity->getEntityType());
        $this->assertNotNull($entity->getEntityId());

        // delete this file
        $client->request('DELETE', '/api/v1/storage/'.$entity->getEntityId());
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
