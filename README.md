# imageCompress

```php
// Путь до файла в файловой системе
$filePath = '';

// Фильтр
$imageFilter = Imagick::FILTER_UNDEFINED;
// Для JPEG можно использовать другой фильтр:
//$imageCompression = Imagick::COMPRESSION_JPEG;

// Формат изображения
$imageFormat = 'gif';
//$imageFormat = 'jpg';
//$imageFormat = 'png';

$imagick = new Imagick($filePath);
$imagick->setImageResolution(72, 72);
$imagick->resampleImage(72, 72, $imageFilter, 1);
$geometry = $imagick->getImageGeometry();

if ($geometry['height'] > 1920 || $geometry['width'] > 1080) {
	$imagick->scaleImage(1920, 0);

	if($geometry['height'] > 1080) {
		$imagick->scaleImage(0, 1080);
	}
}

$imagick->setImageCompression($imageCompression);
$imagick->setImageCompressionQuality(70);
$imagick->setImageFormat($imageFormat);
$imagick->stripImage();
$imagick->writeImage($filePath);
$imagick->clear();
```
