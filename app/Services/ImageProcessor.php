<?php

namespace App\Services;

use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Image;
use Storage;

class ImageProcessor
{
    /**
     * @var array
     */
    private array $dims = array();

    /**
     * @param mixed $contents
     * @param string $filename
     * @param FilesystemAdapter|null $filesystem
     * @param string $directory
     * @param string $encoding
     */
    public function __construct(
        private mixed $contents,
        private string $filename,
        private ?FilesystemAdapter $filesystem = null,
        private string $directory = '/',
        private array $encodings = ['jpg']
    ) {}

    /**
     * @param array $encodings
     * @return void
     */
    public function setEncodings(array $encoding)
    {
        $this->encodings = $encodings;
    }

    /**
     * @param array $dims
     * @return void
     */
    public function setDims(array $dims)
    {
        $this->dims = $dims;
    }

    /**
     * @param string $filename
     * @return void
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param string $directory
     * @return void
     */
    public function setDirectory(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param FilesystemAdapter $filesystem
     * @return void
     */
    public function setFilesystem(FilesystemAdapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return FilesystemAdapter
     */
    private function getFilesystem() : FilesystemAdapter
    {
        return $this->filesystem ?? Storage::disk(config('filesystems.default'));
    }

    /**
     * @return void
     */
    public function process() : void
    {
        $storage = $this->getFilesystem();

        foreach($this->encodings as $encoding) {

            $image = Image::make($this->contents);

            $storage->put($this->directory . '/full/' . $this->filename . '.' . $encoding, $image->encode($encoding, 100));

            foreach($this->dims as $name => $props) {

                $width = isset($props['width']) ? $props['width'] : null;
                $height = isset($props['height']) ? $props['height'] : null;
                $quality = isset($props['quality']) ? $props['quality'] : 100;
                $mode = isset($props['mode']) ? $props['mode'] : 'cover';
                $background = isset($props['background']) ? $props['background'] : '#ffffff';

                $image = Image::make($this->contents);

                if(!is_null($width) || !is_null($height)) {

                    if($mode == 'fit') {

                        if((!is_null($width) && $image->width() >= $width) || (!is_null($height) && $image->height() >= $height)) {
                            $image->resize($width, $height, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }

                    } else {
                        $image->fit($width, $height);
                    }

                    if(!is_null($width) && !is_null($height)) {
                        $canvas = Image::canvas($width, $height, $background);
                    } else {
                        $canvas = Image::canvas($image->width(), $image->height(), $background);
                    }

                    $canvas->insert($image, 'center');
                    $image = $canvas;
                }
                $storage->put($this->directory . '/' . $name . '/' . $this->filename . '.' . $encoding,  $image->encode($encoding, $quality));
            }
        }
    }
}
