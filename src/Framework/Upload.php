<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 15/10/17
 * Time: 10:01
 */

namespace Framework;


use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload {

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $formats = [];

    /**
     * Upload constructor.
     * @param null|string $path
     */
    public function __construct(?string $path = null) {
        if ($path) {
            $this->path = $path;
        }
    }

    /**
     * @param UploadedFileInterface $file
     * @param string $oldFile
     * @return string
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null): string {
        $this->delete($oldFile);
        $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            $old = umask(0);
            mkdir($dirname, 0777, true);
            umask($old);
        }
        $file->moveTo($targetPath);
        $this->generateFormats($targetPath);
        return pathinfo($targetPath)['basename'];
    }

    /**
     * @param string $oldFile
     */
    public function delete(?string $oldFile): void {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }

    /**
     * @param string $targetPath
     * @return string
     */
    private function addCopySuffix(string $targetPath): string {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }

    /**
     * @param $targetPath
     */
    private function generateFormats($targetPath) {
        foreach ($this->formats as $format => $size) {
            $manager = new ImageManager(['driver' => 'gd']);
            $destination = $this->getPathWithSuffix($targetPath, $format);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
    }

    /**
     * @param string $path
     * @param string $suffix
     * @return string
     */
    private function getPathWithSuffix(string $path, string $suffix): string {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $suffix . '.' . $info['extension'];
    }

}