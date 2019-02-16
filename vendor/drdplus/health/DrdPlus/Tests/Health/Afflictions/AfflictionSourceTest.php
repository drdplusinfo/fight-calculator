<?php
namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Health\Afflictions\AfflictionSource;
use Granam\String\StringTools;
use PHPUnit\Framework\TestCase;

class AfflictionSourceTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideSourceCode
     * @param string $sourceCode
     * @param bool $isSomeDeformation
     */
    public function I_can_get_every_source($sourceCode, $isSomeDeformation)
    {
        $getSource = StringTools::assembleGetterForName($sourceCode) . 'Source';
        /** @var AfflictionSource $source */
        $source = AfflictionSource::$getSource();
        self::assertInstanceOf(AfflictionSource::class, $source);
        self::assertSame($source, AfflictionSource::getIt($sourceCode));
        self::assertSame($sourceCode, $source->getValue());
        $isTypeSource = StringTools::assembleGetterForName($source, 'is');
        self::assertTrue($source->$isTypeSource());
        if ($isSomeDeformation) {
            self::assertTrue($source->isDeformation());
        } else {
            self::assertFalse($source->isDeformation(), "Expected source {$source} to be a deformation");
        }

        foreach (array_diff($this->getSourceCodes(), [$sourceCode]) as $otherSourceCode) {
            $isOtherTypeSource = StringTools::assembleGetterForName($otherSourceCode, 'is');
            self::assertFalse($source->$isOtherTypeSource());
        }
    }

    public function provideSourceCode()
    {
        return array_map(
            function ($code) {
                return [$code, strpos($code, 'deformation') !== false];
            },
            $this->getSourceCodes()
        );
    }

    /**
     * @return array|string[]
     */
    private function getSourceCodes(): array
    {
        return [
            'active',
            'passive',
            'full_deformation',
            'partial_deformation',
            'external',
        ];
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Afflictions\Exceptions\UnknownAfflictionSource
     * @expectedExceptionMessageRegExp ~heavy metal~
     */
    public function I_can_not_create_custom_source()
    {
        AfflictionSource::getEnum('heavy metal');
    }
}