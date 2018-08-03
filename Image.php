<?php

namespace Image;

use Imagick;

/**
 * Class Image
 * @package Image
 */
class Image
{
    /**
     * @var int Максимальное разрешение по оси X
     */
    private static $resolutionAxisX = 72;

    /**
     * @var int Максимальное разрешение по оси Y
     */
    private static $resolutionAxisY = 72;

    /**
     * @var int Максимальный размер изображения по ширине.
     *          В случае, если размер картинки по ширине больше этого значения,
     *          то изображение будет пропорционально уменьшено до этого значения
     */
    private static $maxPixelSizeX = 1920;

    /**
     * @var int Максимальный размер изображения по высоте.
     *          В случае, если размер картинки по высоте больше этого значения,
     *          то изображение будет пропорционально уменьшено до этого значения
     */
    private static $maxPixelSizeY = 1080;

    /**
     * @var int Степень сжатия изображения
     */
    private static $quality = 70;

    /**
     * Оптимизиурет изображение для показа в Web
     *
     * @param string $imagePath
     * @param string $imageSavePath
     *
     * @return bool
     */
    public static function optimizeImage($imagePath, $imageSavePath = null)
    {
        try {
            if (!class_exists(Imagick::class)) {
                throw new \RuntimeException(
                    'Class ' . Imagick::class . ' not found'
                );
            }

            $imagick = new Imagick($imagePath);

            $imageResolution = $imagick->getImageResolution();

            if (!$imageResolution
                || !isset($imageResolution['x'])
                || !isset($imageResolution['y'])
                || $imageResolution['x'] <= 0
                || $imageResolution['y'] <= 0
            ) {
                throw new \InvalidArgumentException(
                    'Wrong image resolution'
                );
            }

            $imagick->setImageResolution(
                self::$resolutionAxisX,
                self::$resolutionAxisY
            );
            $imagick->resampleImage(
                self::$resolutionAxisX,
                self::$resolutionAxisY,
                imagick::FILTER_UNDEFINED,
                1
            );

            $geometry = $imagick->getImageGeometry();
            if ($geometry['height'] > self::$maxPixelSizeX
                || $geometry['width'] > self::$maxPixelSizeY
            ) {
                $imagick->scaleImage(self::$maxPixelSizeX, 0);

                if ($geometry['height'] > self::$maxPixelSizeY) {
                    $imagick->scaleImage(0, self::$maxPixelSizeY);
                }
            }

            $imagick->setImageCompression(Imagick::COMPRESSION_UNDEFINED);

            $imagick->setImageCompressionQuality(self::$quality);

            //$imagick->setImageFormat("jpg");

            $imagick->stripImage();

            if ($imageSavePath) {
                $imagick->writeImage($imageSavePath);
            } else {
                $imagick->writeImage($imagePath);
            }

            $imagick->clear();
        } catch (\Exception $E) {
            return false;
        }

        return true;
    }

    /**
     * @param array|null $imagesPaths
     *
     * @return \Generator
     */
    public static function optimizeImages(array $imagesPaths = null)
    {
        foreach ($imagesPaths as $imageId => $imagesPath) {
            yield self::optimizeImage($imagesPath);
        }
    }
}
