<?php

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Helper;

use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use ImagickException;
use medcenter24\mcCore\App\Helpers\FileHelper;

class FakeImage
{
    /**
     * @param string $fileName
     * @return File
     */
    public static function getImage(string $fileName = 'fakeImg.png'): File
    {
        if(!function_exists('imagejpeg')) {
            try {
                $path = FakeImagickImage::createTmpImage($fileName);
            } catch (ImagickException $e) {
                Log::warning($e->getMessage());
                $path = FileHelper::getTmpFilePath();
            }
            $src = fopen($path, 'r');
            return new File($fileName, $src);
        } else {
            return UploadedFile::fake()->image($fileName);
        }
    }
}
