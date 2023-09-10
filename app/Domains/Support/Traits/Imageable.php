<?php

namespace App\Domains\Support\Traits;

use App\Domains\Support\Services\ImageProcessor;
use finfo;
use Illuminate\Filesystem\FilesystemAdapter;
use Storage;
use Str;

trait Imageable
{
    public function imageDims(): array
    {
        return [];
    }

    public function imageStorageDisk(): string
    {
        return config('filesystems.default');
    }

    public function imageStorageDriver(): FilesystemAdapter
    {
        return Storage::disk($this->imageStorageDisk());
    }

    public function imageBaseDirectory(): string
    {
        return 'imageable-images';
    }

    public function imageFilenameProperty(): string
    {
        return 'filename';
    }

    public function imageIdProperty(): string
    {
        return 'uuid';
    }

    /**
     * @return array<int, string>
     */
    public function imageEncodings(): array
    {
        return [
            'jpg',
        ];
    }

    public function imageDirectory(): string
    {
        return $this->imageBaseDirectory().'/'.$this->{$this->imageIdProperty()};
    }

    public function imageUrl(string $size = 'full', ?string $filename = null, ?string $extension = null): string
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

        return $this->imageStorageDriver()->url($this->imageDirectory().'/'.$size.'/'.$filename.'.'.$extension);
    }

    public function imageProcess(mixed $contents, string $filename): void
    {
        try {
            //if image is svg it cannot be processed by the image processor so just encode it for use inline.
            $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($contents));
            if ($mime === 'image/svg+xml') {
                $filename = 'data:image/svg+xml;charset=UTF-8,'.rawurlencode(file_get_contents($contents));
            } else {
                $processor = new ImageProcessor($contents, $filename, $this->imageStorageDriver(), $this->imageDirectory(), $this->imageEncodings());
                $processor->setDims($this->imageDims());
                $processor->process();
            }

            $this->{$this->imageFilenameProperty()} = $filename;
        } catch (\Exception $e) {
            throw new \Exception('Image processor failed: '.$e->getMessage());
        }
    }

    public function imageDelete(): void
    {
        $this->imageStorageDriver()->deleteDirectory($this->imageDirectory());
    }
}
