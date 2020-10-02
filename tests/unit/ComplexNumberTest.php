<?php

declare(strict_types=1);

namespace Remergeit\Tests\Math;

use Remergeit\Math\ComplexNumber;
use PHPUnit\Framework\TestCase;

class ComplexNumberTest extends TestCase
{
    /**
     * @var array[] Test dataset
     */
    private $twoComplexNumberValueDataSets = [
        [123,      null,  456,      null],
        [123.456,  null,  789.012,  null],
        [123.456,  78.90, -987.654, -32.1],
        [123.456,  78.90, -987.654, null],
        [-987.654, -32.1, 0,        1],
        [-987.654, -32.1, 0,        -1],
    ];

    /**
     * @return array[]
     */
    public function providerInstantiateWithArgument(): array
    {
        return array_chunk(array_merge(...$this->twoComplexNumberValueDataSets), 1);
    }

    /**
     * @return array[]
     */
    public function providerInstantiateWithArguments(): array
    {
        return array_chunk(array_merge(...$this->twoComplexNumberValueDataSets), 2);
    }

    /**
     * @return array[]
     */
    public function providerToString(): array
    {
        $expectedResults = [
            ['123+0i'],
            ['456+0i'],
            ['123.456+0i'],
            ['789.012+0i'],
            ['123.456+78.9i'],
            ['-987.654-32.1i'],
            ['-987.654+0i'],
            ['0+1i'],
            ['0-1i'],
        ];
        $serialized = array_map('serialize', $this->providerInstantiateWithArguments());
        $unique = array_unique($serialized);
        $oneComplexNumberValueDataSets = array_intersect_key($this->providerInstantiateWithArguments(), $unique);

        return array_map('array_merge', $oneComplexNumberValueDataSets, $expectedResults);
    }

    /**
     * @return array[]
     */
    public function providerAdd(): array
    {
        $expectedResults = [
            [579,      0],
            [912.468,  0],
            [-864.198, 46.8],
            [-864.198, 78.9],
            [-987.654, -31.1],
            [-987.654, -33.1],
        ];

        return array_map('array_merge', $this->twoComplexNumberValueDataSets, $expectedResults);
    }

    /**
     * @return array[]
     */
    public function providerSubtract(): array
    {
        $expectedResults = [
            [-333, 0],
            [-665.556, 0],
            [1111.11, 111],
            [1111.11, 78.9],
            [-987.654, -33.1],
            [-987.654, -31.1],
        ];

        return array_map('array_merge', $this->twoComplexNumberValueDataSets, $expectedResults);
    }

    /**
     * @return array[]
     */
    public function providerMultiply(): array
    {
        $expectedResults = [
            [56088, 0],
            [97408.265472, 0],
            [-119399.122224, -81888.8382],
            [-121931.812224, -77925.9006],
            [32.1, -987.654],
            [-32.1, 987.654],
        ];

        return array_map('array_merge', $this->twoComplexNumberValueDataSets, $expectedResults);
    }

    /**
     * @return array[]
     */
    public function providerDivide(): array
    {
        $expectedResults = [
            [0.26973684210526, 0],
            [0.15646910313151, 0],
            [-0.127461004165656, -0.07574363265504158],
            [-0.1249992406247532, -0.0798862759630397],
            [-32.1, 987.654],
            [32.1, -987.654],
        ];

        return array_map('array_merge', $this->twoComplexNumberValueDataSets, $expectedResults);
    }

    /**
     * @return void
     */
    public function testInstantiate(): void
    {
        $complexNumber = new ComplexNumber();
        $this->assertTrue(is_object($complexNumber));
        $this->assertTrue(is_a($complexNumber, 'Remergeit\Math\ComplexNumber'));

        $complexNumberReal = $complexNumber->getReal();
        $this->assertEquals(0.0, $complexNumberReal);

        $complexNumberImaginary = $complexNumber->getImaginary();
        $this->assertEquals(0.0, $complexNumberImaginary);
    }

    /**
     * @dataProvider providerInstantiateWithArgument
     * @param mixed $arg
     * @return void
     */
    public function testInstantiateWithArgument($arg): void
    {
        $complexNumber = new ComplexNumber($arg);

        $complexNumberReal = $complexNumber->getReal();
        $this->assertEquals($arg ?? 0.0, $complexNumberReal);

        $complexNumberImaginary = $complexNumber->getImaginary();
        $this->assertEquals(0.0, $complexNumberImaginary);
    }

    /**
     * @dataProvider providerInstantiateWithArguments
     * @param mixed $real
     * @param mixed $imaginary
     * @return void
     */
    public function testInstantiateWithArguments($real, $imaginary): void
    {
        $complexNumber = new ComplexNumber($real, $imaginary);

        $complexNumberReal = $complexNumber->getReal();
        $this->assertEquals($real ?? 0.0, $complexNumberReal);

        $complexNumberImaginary = $complexNumber->getImaginary();
        $this->assertEquals($imaginary ?? 0.0, $complexNumberImaginary);
    }

    /**
     * @return void
     */
    public function testInvalidComplexNumber(): void
    {
        $complexNumber = new ComplexNumber('ABCDE');

        $complexNumberReal = $complexNumber->getReal();
        $this->assertEquals(0.0, $complexNumberReal);

        $complexNumberImaginary = $complexNumber->getImaginary();
        $this->assertEquals(0.0, $complexNumberImaginary);
    }

    /**
     * @dataProvider providerToString
     * @param mixed $real
     * @param mixed $imaginary
     * @param string $expected
     * @return void
     */
    public function testToString($real, $imaginary, string $expected): void
    {
        $complexNumber = new ComplexNumber($real, $imaginary);

        $complexNumberAsString = (string)$complexNumber;
        $this->assertEquals($expected, $complexNumberAsString);
    }

    /**
     * @dataProvider providerAdd
     * @param mixed $firstReal
     * @param mixed $firstImaginary
     * @param mixed $secondReal
     * @param mixed $secondImaginary
     * @param float|int $expectedReal
     * @param float|int $expectedImaginary
     * @return void
     */
    public function testAdd(
        $firstReal,
        $firstImaginary,
        $secondReal,
        $secondImaginary,
        $expectedReal,
        $expectedImaginary
    ): void {
        $firstComplexNumber = new ComplexNumber($firstReal, $firstImaginary);
        $result = $firstComplexNumber->add(
            new ComplexNumber($secondReal, $secondImaginary)
        );

        $expected = new ComplexNumber($expectedReal, $expectedImaginary);
        $this->assertEquals($expected->getReal(), $result->getReal());
        $this->assertEquals($expected->getImaginary(), $result->getImaginary());

        // Verify that the original complex number remains unchanged
        $this->assertEquals(new ComplexNumber($firstReal, $firstImaginary), $firstComplexNumber);
    }

    /**
     * @dataProvider providerSubtract
     * @param mixed $firstReal
     * @param mixed $firstImaginary
     * @param mixed $secondReal
     * @param mixed $secondImaginary
     * @param float|int $expectedReal
     * @param float|int $expectedImaginary
     * @return void
     */
    public function testSubtract(
        $firstReal,
        $firstImaginary,
        $secondReal,
        $secondImaginary,
        $expectedReal,
        $expectedImaginary
    ): void {
        $firstComplexNumber = new ComplexNumber($firstReal, $firstImaginary);
        $result = $firstComplexNumber->subtract(
            new ComplexNumber($secondReal, $secondImaginary)
        );

        $expected = new ComplexNumber($expectedReal, $expectedImaginary);
        $this->assertEquals($expected->getReal(), $result->getReal());
        $this->assertEquals($expected->getImaginary(), $result->getImaginary());

        // Verify that the original complex number remains unchanged
        $this->assertEquals(new ComplexNumber($firstReal, $firstImaginary), $firstComplexNumber);
    }

    /**
     * @dataProvider providerMultiply
     * @param mixed $firstReal
     * @param mixed $firstImaginary
     * @param mixed $secondReal
     * @param mixed $secondImaginary
     * @param float|int $expectedReal
     * @param float|int $expectedImaginary
     * @return void
     */
    public function testMultiply(
        $firstReal,
        $firstImaginary,
        $secondReal,
        $secondImaginary,
        $expectedReal,
        $expectedImaginary
    ): void {
        $firstComplexNumber = new ComplexNumber($firstReal, $firstImaginary);
        $result = $firstComplexNumber->multiply(
            new ComplexNumber($secondReal, $secondImaginary)
        );

        $expected = new ComplexNumber($expectedReal, $expectedImaginary);
        $this->assertEquals($expected->getReal(), $result->getReal());
        $this->assertEquals($expected->getImaginary(), $result->getImaginary());

        // Verify that the original complex number remains unchanged
        $this->assertEquals(new ComplexNumber($firstReal, $firstImaginary), $firstComplexNumber);
    }

    /**
     * @dataProvider providerDivide
     * @param mixed $firstReal
     * @param mixed $firstImaginary
     * @param mixed $secondReal
     * @param mixed $secondImaginary
     * @param float|int $expectedReal
     * @param float|int $expectedImaginary
     * @return void
     */
    public function testDivide(
        $firstReal,
        $firstImaginary,
        $secondReal,
        $secondImaginary,
        $expectedReal,
        $expectedImaginary
    ): void {
        $firstComplexNumber = new ComplexNumber($firstReal, $firstImaginary);
        $result = $firstComplexNumber->divide(
            new ComplexNumber($secondReal, $secondImaginary)
        );

        $expected = new ComplexNumber($expectedReal, $expectedImaginary);
        $this->assertEquals($expected->getReal(), $result->getReal());
        $this->assertEquals($expected->getImaginary(), $result->getImaginary());

        // Verify that the original complex number remains unchanged
        $this->assertEquals(new ComplexNumber($firstReal, $firstImaginary), $firstComplexNumber);
    }

    /**
     * @return void
     */
    public function testDivideByZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $firstComplexNumber = new ComplexNumber(123, null);
        $firstComplexNumber->divide(new ComplexNumber());
    }
}
