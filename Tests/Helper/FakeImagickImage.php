<?php

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Helper;

use Imagick;
use ImagickException;
use medcenter24\mcCore\App\Helpers\FileHelper;

class FakeImagickImage
{
    /**
     * @param string $labelText
     * @param int $width
     * @param int $height
     * @return Imagick
     * @throws ImagickException
     */
    private static function createPseudoImage(string $labelText, int $width, int $height): Imagick
    {
        $background = new Imagick();
        $background->newPseudoImage($width, $height, 'PATTERN:HORIZONTALSAW');

        $label = new Imagick();
        $label->setBackgroundColor('transparent');
        $labelWidth = (int) ceil($background->getImageWidth() * 0.8);
        $labelHeight = (int) ceil($background->getImageHeight() * 0.8);
        $labelFormat='No. %s';
        $labelText = sprintf('CAPTION:'.$labelFormat, $labelText);
        $label->newPseudoImage($labelWidth, $labelHeight, $labelText);
        $offsetX = $background->getImageWidth()/2 - $label->getImageWidth()/2;
        $offsetY = $background->getImageHeight()/2 - $label->getImageHeight()/2;
        $background->compositeImage($label, Imagick::COMPOSITE_ATOP, $offsetX, $offsetY);
        return $background;
    }

    /**
     * @param string $text
     * @param int $width
     * @param int $height
     * @return string
     * @throws ImagickException
     */
    public static function createTmpImage(string $text = 'img', int $width = 100, int $height = 100): string
    {
        $fpo = self::createPseudoImage($text, $width, $height);
        $path = FileHelper::getTmpFilePath();
        $newPath = $path.$text;
        rename($path, $newPath);
        $fpo->writeImage($newPath);
        return $newPath;
    }
}
