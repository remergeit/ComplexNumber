<?php

declare(strict_types=1);

namespace Remergeit\Math;

/**
 * Class for calculating complex numbers
 *
 * @method ComplexNumber add(ComplexNumber $complexNumber)
 * @method ComplexNumber subtract(ComplexNumber $complexNumber)
 * @method ComplexNumber multiply(ComplexNumber $complexNumber)
 * @method ComplexNumber divide(ComplexNumber $complexNumber)
 */
class ComplexNumber
{
    /**
     * @var float Real part
     */
    private $real;

    /**
     * @var float Imaginary part
     */
    private $imaginary;

    public function __construct($real = 0.0, $imaginary = 0.0)
    {
        $this->real = (float) $real;
        $this->imaginary = (float) $imaginary;
    }

    public function __toString(): string
    {
        if ($this->imaginary >= 0) {
            return $this->real . '+' . $this->imaginary . 'i';
        } else {
            return $this->real . $this->imaginary . 'i';
        }
    }

    /**
     * Gets the real part of this complex number
     *
     * @return float
     */
    public function getReal(): float
    {
        return $this->real;
    }

    /**
     * Gets the imaginary part of this complex number
     *
     * @return float
     */
    public function getImaginary(): float
    {
        return $this->imaginary;
    }

    /**
     * Adds two complex numbers
     *
     * @param ComplexNumber $other
     * @return ComplexNumber
     */
    public function add(ComplexNumber $other): ComplexNumber
    {
        $real = $this->real + $other->getReal();
        $imaginary = $this->imaginary + $other->getImaginary();

        return new ComplexNumber($real, $imaginary);
    }

    /**
     * Subtracts two complex numbers
     *
     * @param ComplexNumber $other
     * @return ComplexNumber
     */
    public function subtract(ComplexNumber $other): ComplexNumber
    {
        $real = $this->real - $other->getReal();
        $imaginary = $this->imaginary - $other->getImaginary();

        return new ComplexNumber($real, $imaginary);
    }

    /**
     * Multiplies two complex numbers
     *
     * @param ComplexNumber $other
     * @return ComplexNumber
     */
    public function multiply(ComplexNumber $other): ComplexNumber
    {
        $real = ($this->real * $other->getReal() - $this->imaginary * $other->getImaginary());
        $imaginary = ($this->imaginary * $other->getReal() + $this->real * $other->getImaginary());

        return new ComplexNumber($real, $imaginary);
    }

    /**
     * Divides two complex numbers
     *
     * @param ComplexNumber $other
     * @return ComplexNumber
     * @throws \InvalidArgumentException If the argument is a zero Complex number
     */
    public function divide(ComplexNumber $other): ComplexNumber
    {
        if ($other->getReal() == 0.0 && $other->getImaginary() == 0.0) {
            throw new \InvalidArgumentException('Division by zero');
        }
        $real = ($this->real * $other->getReal() + $this->imaginary * $other->getImaginary())
                / ($other->getReal() ** 2 + $other->getImaginary() ** 2);
        $imaginary = ($this->imaginary * $other->getReal() - $this->real * $other->getImaginary())
                     / ($other->getReal() ** 2 + $other->getImaginary() ** 2);

        return new ComplexNumber($real, $imaginary);
    }
}
