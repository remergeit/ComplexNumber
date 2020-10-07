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
        if (func_num_args() === 1 && is_string(func_get_arg(0))) {
            $rectangularRegex =
                // Match any float, negative or positive, its Real part, group 1
                '/^\s*([-+]?\d+|[-+]?\d+\.?\d+)' .
                // ... possibly following that with whitespaces
                '\s*' .
                // Match any other float followed by 'i', its Imaginary part, group 2
                '([-+]?\s*\d+|[-+]?\s*\d+\.?\d+)\s*[i]\s*$/';
            $rectangularSuccess = preg_match($rectangularRegex, func_get_arg(0), $rectangularMatch);
            if ($rectangularSuccess) {
                $this->real = (float) $rectangularMatch[1];
                $this->imaginary = (float) $rectangularMatch[2];
                return;
            }
            $polarRegex =
                // Match only positive float, its Modulus, group 1
                '/^\s*([+]?\d+|[+]?\d+\.?\d+)' .
                // ... possibly following that with whitespaces or ','
                '\s*[,]?\s+' .
                // Match other only positive float, possibly followed by '°', its Degree, group 2
                '([+]?\d+|[+]?\d+\.?\d+)\s*[\xB0|\x{00B0}]?\s*$/u';
            $polarSuccess = preg_match($polarRegex, func_get_arg(0), $polarMatch);
            if ($polarSuccess) {
                $modulus = (float) $polarMatch[1];
                $degree = (float) $polarMatch[2];
                $this->real = $modulus * cos(deg2rad($degree));
                $this->imaginary = $modulus * sin(deg2rad($degree));
                return;
            }
            $polarHumanRegex =
                // Match only positive float, its Modulus, group 1
                '/^\s*([+]?\d+|[+]?\d+\.?\d+)' .
                // ... possibly following that with whitespaces, '*' or 'x' before first round bracket
                '\s*[*x]?\s*[(]\s*' .
                // Match first 'cos' or 'i sin' inside round brackets
                '(?:(?:(?:(cos)|i\s*[*x]?\s*(sin))\s*' .
                // Match other only positive float, possibly followed by '°' and in round brackets, its Degree, group 4
                '[(]?\s*([+]?\d+|[+]?\d+\.?\d+)\s*[\xB0|\x{00B0}]?\s*[)]?)' .
                // ... following that with '+'
                '\s*[+]\s*' .
                // Match second 'cos' or 'i sin' inside round brackets, should not be the same as first
                '(?:(?:(?!\2)(cos)|i\s*[*x]?\s*(?!\3)(sin))\s*' .
                // Match Degree one more time, should be the same as first
                '[(]?\s*(\4)\s*[\xB0|\x{00B0}]?\s*[)]?))' .
                // ... possibly following that with whitespaces before and after last round bracket
                '\s*[)]\s*$/u';
            $polarHumanSuccess = preg_match($polarHumanRegex, func_get_arg(0), $polarHumanMatch);
            if ($polarHumanSuccess) {
                $modulus = (float) $polarHumanMatch[1];
                $degree = (float) $polarHumanMatch[4];
                $this->real = $modulus * cos(deg2rad($degree));
                $this->imaginary = $modulus * sin(deg2rad($degree));
                return;
            }
            throw new \InvalidArgumentException('Parse string failed');
        } else {
            $this->real = (float) $real;
            $this->imaginary = (float) $imaginary;
        }
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
     * Gets the modulus of this complex number
     *
     * @return float
     */
    public function getModulus(): float
    {
        return sqrt($this->real ** 2 + $this->imaginary ** 2);
    }

    /**
     * Gets the degree of this complex number
     *
     * @return float
     */
    public function getDegree(): float
    {
        // tangent undefined at 90, 270 degrees
        if ($this->real === 0.0) {
            $degree = $this->imaginary > 0
                      ? 90
                      : ($this->imaginary < 0
                         ? 270
                         : 0);
            return $degree;
        } else {
            $tangent = abs($this->imaginary) / abs($this->real);
            $degree = rad2deg(atan($tangent));

            $quadrant1 = $this->real > 0 && $this->imaginary >= 0;
            $quadrant2 = $this->real < 0 && $this->imaginary >= 0;
            $quadrant3 = $this->real < 0 && $this->imaginary <= 0;
            $quadrant4 = $this->real > 0 && $this->imaginary <= 0;
            if ($quadrant1) {
                return $degree;
            }
            if ($quadrant2) {
                return 180 - $degree;
            }
            if ($quadrant3) {
                return 180 + $degree;
            }
            if ($quadrant4) {
                return 360 - $degree;
            }
        }
    }

    /**
     * Returns the formatted string representation of this complex number
     *
     * @param string $format
     * @return string
     */
    public function format(string $format): string
    {
        switch ($format) {
            case 'rectangular':
                return (string) $this;
            case 'polar':
                return $this->getModulus() . ', ' . $this->getDegree();
            case 'polarHuman':
                $degree = $this->getDegree();
                return $this->getModulus() . "(cos $degree ° + i sin $degree °)";
            default:
                return (string) $this;
        }
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
