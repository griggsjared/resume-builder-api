<?php

namespace App\Domains\Support\Services;

use Illuminate\Filesystem\FilesystemAdapter;
use Intervention\Image\Image;

class ImageProcessor
{
    private array $dims = [];

    public function __construct(
        private mixed $contents,
        private string $filename,
        private FilesystemAdapter $filesystem,
        private string $directory = '/',
        private array $encodings = ['jpg']
    ) {
    }

    /**
     * @param  array<int, string>  $encodings
     */
    public function setEncodings(array $encoding): void
    {
        $this->encodings = $encodings;
    }

    public function setDims(array $dims): void
    {
        $this->dims = $dims;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function setFilesystem(FilesystemAdapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    private function getFilesystem(): FilesystemAdapter
    {
        return $this->filesystem;
    }

    public function process(): void
    {
        $storage = $this->getFilesystem();

        foreach ($this->encodings as $encoding) {
            $image = Image::make($this->contents);

            $storage->put($this->directory.'/full/'.$this->filename.'.'.$encoding, $image->encode($encoding, 100));

            foreach ($this->dims as $name => $props) {
                $width = isset($props['width']) ? $props['width'] : null;
                $height = isset($props['height']) ? $props['height'] : null;
                $quality = isset($props['quality']) ? $props['quality'] : 100;
                $mode = isset($props['mode']) ? $props['mode'] : 'cover';
                $background = isset($props['background']) ? $props['background'] : '#ffffff';

                $image = Image::make($this->contents);

                if (! is_null($width) || ! is_null($height)) {
                    if ($mode == 'fit') {
                        if ((! is_null($width) && $image->width() >= $width) || (! is_null($height) && $image->height() >= $height)) {
                            $image->resize($width, $height, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                    } else {
                        $image->fit($width, $height);
                    }

                    if (! is_null($width) && ! is_null($height)) {
                        $canvas = Image::canvas($width, $height, $background);
                    } else {
                        $canvas = Image::canvas($image->width(), $image->height(), $background);
                    }

                    $canvas->insert($image, 'center');
                    $image = $canvas;
                }
                $storage->put($this->directory.'/'.$name.'/'.$this->filename.'.'.$encoding, $image->encode($encoding, $quality));
            }
        }
    }
}
