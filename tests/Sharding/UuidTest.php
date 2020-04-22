<?php

declare(strict_types=1);

namespace App\Tests\Sharding;

use App\Exception\WanderlusterException;
use App\Sharding\Uuid;
use PHPUnit\Framework\TestCase;
use Exception;

class UuidTest extends TestCase
{
    public function testConstructor(): void
    {
        $uuidString = '10-3-'.substr(md5('foobar'), 0, 16);
        $sut = new Uuid($uuidString);

        $this->assertEquals(10, $sut->getShard());
        $this->assertEquals(3, $sut->getEntityType());
        $this->assertEquals('3858f62230ac3c91', $sut->getIdentifier());
    }

    public function testToString(): void
    {
        $uuidString = '10-3-'.substr(md5('foobar'), 0, 16);
        $sut = new Uuid($uuidString);

        $this->assertEquals('10-3-3858f62230ac3c91', (string) $sut);
        $this->assertEquals('10-3-3858f62230ac3c91', $sut->asString());
    }

    public function testInvalidUuidFormat(){
        try{
            new Uuid('kevin');
            $this->fail('Exception not thrown');
        } catch(Exception $e){
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid UUID format - kevin', $e->getMessage());
        }

        try{
            new Uuid('01-01-kevin');
            $this->fail('Exception not thrown');
        } catch(Exception $e){
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Invalid UUID format - 01-01-kevin', $e->getMessage());
        }
    }
}
