<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 15/10/17
 * Time: 10:01
 */

namespace Tests\Framework;


use Framework\Upload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadTest extends TestCase {

    /**
     * @var Upload
     */
    private $upload;

    public function setUp() {
        $this->upload = new Upload('tests');
    }

    public function tearDown() {
        if (file_exists('tests/demo.jpg')) {
            unlink('tests/demo.jpg');
        }
    }

    public function testUpload() {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
        $uploadedFile
            ->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_OK);
        $uploadedFile
            ->expects($this->any())
            ->method('getClientFileName')
            ->willReturn('demo.jpg');
        $uploadedFile
            ->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests/demo.jpg'));
        $this->assertEquals('demo.jpg', $this->upload->upload($uploadedFile));
    }

    public function testWithExistinfFile() {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
        $uploadedFile
            ->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_OK);
        $uploadedFile
            ->expects($this->any())
            ->method('getClientFileName')
            ->willReturn('demo.jpg');
        touch('tests/demo.jpg');
        $uploadedFile
            ->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests/demo_copy.jpg'));
        $this->assertEquals('demo_copy.jpg', $this->upload->upload($uploadedFile));
    }

    public function testDontMoveIfFileUploaded() {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
        $uploadedFile
            ->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_CANT_WRITE);
        $uploadedFile
            ->expects($this->any())
            ->method('getClientFileName')
            ->willReturn('demo.jpg');
        $uploadedFile
            ->expects($this->never())
            ->method('moveTo')
            ->with($this->equalTo('tests/demo.jpg'));
        $this->assertNull($this->upload->upload($uploadedFile));
    }

}