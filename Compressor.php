<?php

namespace Image;

use Imagick;

/**
 * Class Compressor
 * @package Image
 */
class Compressor
{
    /**
     * @var int Максимальное разрешение по оси X
     */
    private $resolutionAxisX = 72;

    /**
     * @var int Максимальное разрешение по оси Y
     */
    private $resolutionAxisY = 72;

    /**
     * @var int Максимальный размер изображения по ширине.
     *          В случае, если размер картинки по ширине больше этого значения,
     *          то изображение будет пропорционально уменьшено до этого значения
     */
    private $maxPixelSizeX = 1920;

    /**
     * @var int Максимальный размер изображения по высоте.
     *          В случае, если размер картинки по высоте больше этого значения,
     *          то изображение будет пропорционально уменьшено до этого значения
     */
    private $maxPixelSizeY = 1080;

    /**
     * @var int Степень сжатия изображения
     */
    private $quality = 70;

    /**
     * @var int Уровень blur
     */
    private $blurLevel = 1;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'resolution':
                    $this->setResolution($value);
                    break;
                case 'resolutionX':
                    $this->setResolutionX($value);
                    break;
                case 'resolutionY':
                    $this->setResolutionY($value);
                    break;
                case 'maxHeight':
                    $this->setMaxSizeHeight($value);
                    break;
                case 'maxWidth':
                    $this->setMaxSizeWidth($value);
                    break;
                case 'quality':
                    $this->setQuality($value);
                    break;
                default:
                    break;
            }
        }
    }


    /**
     * @return int Максимальное разрешение по оси X изображения
     */
    public function getResolutionX()
    {
        return $this->resolutionAxisX;
    }

    /**
     * @return int Максимальное разрешение по оси Y изображения
     */
    public function getResolutionY()
    {
        return $this->resolutionAxisX;
    }

    /**
     * @return array Параметры максимального разрешения изображения
     */
    public function getResolution()
    {
        return [
            'x' => $this->getResolutionX(),
            'y' => $this->getResolutionY(),
        ];
    }

    /**
     * Устанавливает параметры максимального разрешения изображения
     *
     * @param int $value
     */
    public function setResolution($value)
    {
        $this->setResolutionX($value);
        $this->setResolutionY($value);
    }

    /**
     * Устанавливает параметры максимального разрешения по оси X изображения
     *
     * @param int $value
     */
    public function setResolutionX($value)
    {
        self::prepareResolution($value);

        $this->resolutionAxisX = $value;
    }

    /**
     * Устанавливает параметры максимального разрешения по оси Y изображения
     *
     * @param int $value
     */
    public function setResolutionY($value)
    {
        self::prepareResolution($value);

        $this->resolutionAxisY = $value;
    }

    /**
     * Подготавливает значение для установки максимального разрешения изображения
     *
     * @param mixed $value
     */
    private static function prepareResolution(&$value)
    {
        $value = \intval($value);

        if ($value < 0) {
            $value = 0;
        } elseif ($value > 100) {
            $value = 100;
        }
    }

    /**
     * @return int Ширина изображения
     */
    public function getMaxSizeWidth()
    {
        return $this->maxPixelSizeX;
    }

    /**
     * @return int Высота изображения
     */
    public function getMaxSizeHeight()
    {
        return $this->maxPixelSizeY;
    }

    /**
     * @return array Параметры размеров изображения
     */
    public function getMaxSize()
    {
        return [
            'height' => $this->getMaxSizeHeight(),
            'width' => $this->getMaxSizeWidth(),
        ];
    }

    /**
     * Устанавливает параметры высоты изображения
     *
     * @param int $value
     */
    public function setMaxSizeHeight($value)
    {
        self::prepareImageSize($value);

        $this->maxPixelSizeY = $value;
    }

    /**
     * Устанавливает параметры ширины изображения
     *
     * @param int $value
     */
    public function setMaxSizeWidth($value)
    {
        self::prepareImageSize($value);

        $this->maxPixelSizeX = $value;
    }

    /**
     * Подготавливает значение для установки параметров размеров изображения
     *
     * @param mixed $value
     */
    private static function prepareImageSize(&$value)
    {
        $value = \intval($value);

        if ($value < 0) {
            $value = 0;
        }
    }

    /**
     * @return int Степень сжатия изображения
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Устанавливает параметры степени сжатия изображения
     *
     * @param int $value
     */
    public function setQuality($value)
    {
        self::prepareQuality($value);

        $this->quality = $value;
    }

    /**
     * Подготавливает значение для установки
     * параметров степени сжатия изображения
     *
     * @param mixed $value
     */
    private static function prepareQuality(&$value)
    {
        $value = \intval($value);

        if ($value < 0) {
            $value = 0;
        } elseif ($value > 100) {
            $value = 100;
        }
    }

    /**
     * Оптимизиурет изображение для показа в Web
     *
     * @param string $imagePath
     * @param string $imageSavePath
     *
     * @return bool
     */
    public function optimizeImage($imagePath, $imageSavePath = null)
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
                $this->resolutionAxisX,
                $this->resolutionAxisY
            );
            $imagick->resampleImage(
                $this->resolutionAxisX,
                $this->resolutionAxisY,
                imagick::FILTER_UNDEFINED,
                $this->blurLevel
            );

            $geometry = $imagick->getImageGeometry();

            if ($geometry['width'] > $this->maxPixelSizeX
                || $geometry['height'] > $this->maxPixelSizeY
            ) {
                if ($geometry['width'] / $geometry['height']
                    > $this->maxPixelSizeX / $this->maxPixelSizeY
                ) {
                    $imagick->scaleImage(0, $this->maxPixelSizeY);
                } else {
                    $imagick->scaleImage($this->maxPixelSizeX, 0);
                }
            }

            $imagick->setImageCompression(Imagick::COMPRESSION_UNDEFINED);

            $imagick->setImageCompressionQuality($this->quality);

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
    public function optimizeImages(array $imagesPaths = null)
    {
        foreach ($imagesPaths as $imageId => $imagesPath) {
            yield $this->optimizeImage($imagesPath);
        }
    }
}
