<?php declare(strict_types=1);

namespace DrdPlus\Tests\Calculations;

use DrdPlus\Calculations\SumAndRound;
use Granam\TestWithMockery\TestWithMockery;

class SumAndRoundTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_round_a_number(): void
    {
        $shouldBeHigher = 4.5;
        self::assertSame(5, SumAndRound::round($shouldBeHigher));
        $shouldBeLower = 4.4;
        self::assertSame(4, SumAndRound::round($shouldBeLower));
    }

    /**
     * @test
     */
    public function I_can_floor_a_number(): void
    {
        $almostHigherInteger = 5.999999;
        self::assertSame(5, SumAndRound::floor($almostHigherInteger));
    }

    /**
     * @test
     */
    public function I_can_ceil_a_number(): void
    {
        $almostHigherInteger = 5.999999;
        self::assertSame(6, SumAndRound::ceil($almostHigherInteger));
        $integerAndBit = 8.0000001;
        self::assertSame(9, SumAndRound::ceil($integerAndBit));
    }

    /**
     * @test
     */
    public function I_can_get_round_average_of_two_numbers(): void
    {
        $firstNumber = 1.5123;
        $secondNumber = 2.5123;
        self::assertSame(2, SumAndRound::average($firstNumber, $secondNumber));
        $firstNumber = 0.49999;
        $secondNumber = 2.4999;
        self::assertSame(1, SumAndRound::average($firstNumber, $secondNumber));
    }

    /**
     * @test
     */
    public function I_can_get_round_half(): void
    {
        $number = 5;
        self::assertSame(3, SumAndRound::half($number));
        $number = 4.99;
        self::assertSame(2, SumAndRound::half($number));
    }

    /**
     * @test
     */
    public function I_can_get_floored_half(): void
    {
        $number = 5;
        self::assertSame(2, SumAndRound::flooredHalf($number));
        $number = 4.0001;
        self::assertSame(2, SumAndRound::flooredHalf($number));
        $number = 3.999999;
        self::assertSame(1, SumAndRound::flooredHalf($number));
    }

    /**
     * @test
     */
    public function I_can_get_ceiled_half(): void
    {
        $number = 5;
        self::assertSame(3, SumAndRound::ceiledHalf($number));
        $number = 4.0001;
        self::assertSame(3, SumAndRound::ceiledHalf($number));
        $number = 4;
        self::assertSame(2, SumAndRound::ceiledHalf($number));
    }

    /**
     * @test
     */
    public function I_can_get_third(): void
    {
        $number = 7.5;
        self::assertSame(3, SumAndRound::third($number));
        $number = 2.9999;
        self::assertSame(1, SumAndRound::third($number));
        $number = 1.49;
        self::assertSame(0, SumAndRound::third($number));
    }

    /**
     * @test
     */
    public function I_can_get_ceiled_third(): void
    {
        $number = 5;
        self::assertSame(2, SumAndRound::ceiledThird($number));
        $number = 2.9999;
        self::assertSame(1, SumAndRound::ceiledThird($number));
        $number = 98.7;
        self::assertSame(33, SumAndRound::ceiledThird($number));
    }

    /**
     * @test
     */
    public function I_can_get_floored_third(): void
    {
        $number = 5;
        self::assertSame(1, SumAndRound::flooredThird($number));
        $number = 2.9999;
        self::assertSame(0, SumAndRound::flooredThird($number));
        $number = 99;
        self::assertSame(33, SumAndRound::flooredThird($number));
    }
}
