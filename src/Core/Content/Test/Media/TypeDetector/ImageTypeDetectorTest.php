<?php declare(strict_types=1);

namespace Shopware\Core\Content\Test\Media\TypeDetector;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaType\ImageType;
use Shopware\Core\Content\Media\MediaType\VideoType;
use Shopware\Core\Content\Media\TypeDetector\ImageTypeDetector;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

class ImageTypeDetectorTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testDetectGif(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/logo.gif'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectGifDoesNotOverwriteButAddsFlags(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/logo.gif'),
            new VideoType()
        );

        static::assertInstanceOf(VideoType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectAnimatedGif(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/animated.gif'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(2, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
        static::assertTrue($type->is(ImageType::ANIMATED));
    }

    public function testDetectAnimatedGifFromUrl(): void
    {
        static::markTestSkipped('NEXT-15949 - Do not fetch external resources in unit tests');

        $type = $this->getImageTypeDetector()->detect(
            new MediaFile(
                'https://upload.wikimedia.org/wikipedia/commons/f/f0/Zipper_animated.gif',
                'image/gif',
                'gif',
                1024
            ),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(2, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
        static::assertTrue($type->is(ImageType::ANIMATED));
    }

    public function testDetectWebp(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/shopware.webp'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectAnimatedWebp(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/animated.webp'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(2, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
        static::assertTrue($type->is(ImageType::ANIMATED));
    }

    public function testDetectAnimatedWebpFromUrl(): void
    {
        static::markTestSkipped('NEXT-15949 - Do not fetch external resources in unit tests');

        $type = $this->getImageTypeDetector()->detect(
            new MediaFile(
                'https://upload.wikimedia.org/wikipedia/commons/f/f8/Simple_Animated_Clock.webp',
                'image/webp',
                'webp',
                1024
            ),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(2, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
        static::assertTrue($type->is(ImageType::ANIMATED));
    }

    public function testDetectSvg(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/logo-version-professionalplus.svg'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::VECTOR_GRAPHIC));
    }

    public function testDetectJpg(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/shopware.jpg'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(0, $type->getFlags());
    }

    public function testDetectPng(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/shopware-logo.png'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectWorksForUpperCase(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/shopware-logo-1.PNG'),
            null
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectDoc(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/reader.doc'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectDocx(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/reader.docx'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectPdf(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/Shopware_5_3_Broschuere.pdf'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectAvi(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.avi'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectMov(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.mov'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectMp4(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.mp4'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectWebm(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.webm'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectIso(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/test.iso'),
            null
        );

        static::assertNull($type);
    }

    public function testDetectMp3(): void
    {
        $type = $this->getImageTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/file_example.mp3'),
            null
        );

        static::assertNull($type);
    }

    private function getImageTypeDetector(): ImageTypeDetector
    {
        return $this->getContainer()->get(ImageTypeDetector::class);
    }

    private function createMediaFile(string $filePath): MediaFile
    {
        return new MediaFile(
            $filePath,
            mime_content_type($filePath),
            pathinfo($filePath, \PATHINFO_EXTENSION),
            filesize($filePath)
        );
    }
}
