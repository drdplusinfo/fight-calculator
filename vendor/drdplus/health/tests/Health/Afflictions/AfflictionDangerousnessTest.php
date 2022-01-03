<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions;

use Granam\IntegerEnum\IntegerEnum;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use PHPUnit\Framework\TestCase;

class AfflictionDangerousnessTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        for ($dangerousness = -999; $dangerousness < 1000; $dangerousness += 123) {
            $afflictionDangerousness = AfflictionDangerousness::getIt($dangerousness);
            self::assertInstanceOf(AfflictionDangerousness::class, $afflictionDangerousness);
            self::assertInstanceOf(IntegerEnum::class, $afflictionDangerousness);
            self::assertSame($dangerousness, $afflictionDangerousness->getValue());
        }
    }
}