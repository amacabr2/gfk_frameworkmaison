<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 15/10/17
 * Time: 10:01
 */

namespace Framework;


use Psr\Http\Message\UploadedFileInterface;

class Upload {

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $formats;

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
        $targetPath = $this->addSuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            $old = umask(0);
            mkdir($dirname, 0777, true);
            umask($old);
        }
        $file->moveTo($targetPath);
        return pathinfo($targetPath)['basename'];
    }

    /**
     * @param string $targetPath
     * @return string
     */
    private function addSuffix(string $targetPath): string {
        if (file_exists($targetPath)) {
            $info = pathinfo($targetPath);
            $targetPath = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_copy.' . $info['extension'];
            return $this->addSuffix($targetPath);
        }
        return $targetPath;
    }

    /**
     * @param string $oldFile
     */
    private function delete(?string $oldFile): void {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

}