<?php
namespace App\Models\Traits;

use Storage;
use Str;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Services\ImageProcessor;
use finfo;

trait Imageable
{
    /**
     * @return array
     */
    public function imageDims() : array
    {
        return [];
    }

    /**
     * @return string
     */
    public function imageStorageDisk() : string
    {
        return config('filesystems.default');
    }

    /**
     * @return FilesystemAdapter
     */
    public function imageStorageDriver() : FilesystemAdapter
    {
        return Storage::disk($this->imageStorageDisk());
    }

    /**
     * @return string
     */
    public function imageBaseDirectory() : string
    {
        return 'imageable-images';
    }

    /**
     * @return string
     */
    public function imageFilenameProperty() : string
    {
        return 'filename';
    }

    /**
     * @return string
     */
    public function imageIdProperty() : string
    {
        return 'uuid';
    }

    /**
     * @return array
     */
    public function imageEncodings() : array
    {
        return [
            'jpg'
        ];
    }

    /**
     * @return string
     */
    public function imageDirectory() : string
    {
        return $this->imageBaseDirectory() . '/' . $this->{$this->imageIdProperty()};
    }

    /**
     * @param string|null $size
     * @param boolean $checkSource
     * @return string
     */
    public function imageUrl(string $size = 'full', ?string $filename = null, ?string $extension = null) : string
    {

        $filename = $filename ? $filename : $this->{$this->imageFilenameProperty()};

        if (filter_var($filename, FILTER_VALIDATE_URL)) {
            return $filename;
        }

        if (Str::startsWith($filename, 'data:image')) {
            return $filename;
        }

        $extension = $extension ?? $this->imageEncodings()[0];

        $size = isset($this->imageDims()[$size]) ? $size : 'full';

        return $this->imageStorageDriver()->url( $this->imageDirectory() . '/' . $size . '/' . $filename . '.' . $extension);
    }

    /**
     * @param mixed $contents
     * @param string $filename
     * @param string $encoding
     * @return void
     */
    public function imageProcess(mixed $contents, string $filename) : void
    {
        try {
            //if image is svg it cannot be processed by the image processor so just encode it for use inline.
            $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($contents));
            if($mime === 'image/svg+xml') {
                $filename = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode(file_get_contents($contents));
            } else {
                $processor = new ImageProcessor($contents, $filename, $this->imageStorageDriver(), $this->imageDirectory(), $this->imageEncodings());
                $processor->setDims($this->imageDims());
                $processor->process();
            }

            $this->{$this->imageFilenameProperty()} = $filename;

        } catch(\Exception $e) {
            throw new \Exception('Image processor failed: ' . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function imageDelete() : void
    {
        $this->imageStorageDriver()->deleteDirectory($this->imageDirectory());
    }
}
