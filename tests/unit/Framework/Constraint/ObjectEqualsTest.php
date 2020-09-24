<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\TestFixture\ObjectEquals\ValueObject;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatAcceptsTooManyArguments;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatDoesNotAcceptArguments;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatDoesNotDeclareParameterType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatHasIncompatibleParameterType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatHasUnionParameterType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithNullableReturnType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithoutReturnType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithUnionReturnType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithVoidReturnType;
use PHPUnit\TestFixture\ObjectEquals\ValueObjectWithoutEqualsMethod;

/**
 * @covers \PHPUnit\Framework\Constraint\ObjectEquals
 *
 * @small
 */
final class ObjectEqualsTest extends TestCase
{
    public function testAcceptsActualObjectWhenMethodSaysTheyAreEqual(): void
    {
        $this->assertTrue((new ObjectEquals(new ValueObject(1)))->evaluate(new ValueObject(1), '', true));
    }

    public function testRejectsActualValueThatIsNotAnObject(): void
    {
        $this->expectFailure('Actual value is not an object.');

        (new ObjectEquals(new ValueObject(1)))->evaluate(null);
    }

    public function testRejectsActualObjectThatDoesNotHaveTheSpecifiedMethod(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithoutEqualsMethod::equals() does not exist.');

        (new ObjectEquals(new ValueObjectWithoutEqualsMethod(1)))->evaluate(new ValueObjectWithoutEqualsMethod(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodExistsButIsNotDeclaredToReturnBool(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithoutReturnType::equals() does not declare a bool return type.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodWithoutReturnType(1)))->evaluate(new ValueObjectWithEqualsMethodWithoutReturnType(1));
    }

    /**
     * @requires PHP 8
     */
    public function testRejectsActualObjectWhenTheSpecifiedMethodExistsButIsDeclaredToReturnUnion(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithUnionReturnType::equals() does not declare a bool return type.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodWithUnionReturnType(1)))->evaluate(new ValueObjectWithEqualsMethodWithUnionReturnType(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodExistsButIsDeclaredVoid(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithVoidReturnType::equals() does not declare a bool return type.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodWithVoidReturnType(1)))->evaluate(new ValueObjectWithEqualsMethodWithVoidReturnType(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodExistsButIsDeclaredNullable(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodWithNullableReturnType::equals() does not declare a bool return type.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodWithNullableReturnType(1)))->evaluate(new ValueObjectWithEqualsMethodWithNullableReturnType(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodDoesNotAcceptArguments(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatDoesNotAcceptArguments::equals() does not accept exactly one argument.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodThatDoesNotAcceptArguments(1)))->evaluate(new ValueObjectWithEqualsMethodThatDoesNotAcceptArguments(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodAcceptsTooManyArguments(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatAcceptsTooManyArguments::equals() does not accept exactly one argument.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodThatAcceptsTooManyArguments(1)))->evaluate(new ValueObjectWithEqualsMethodThatAcceptsTooManyArguments(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodDoesNotDeclareParameterType(): void
    {
        $this->expectFailure('Parameter of PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatDoesNotDeclareParameterType::equals() does not have a declared type.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodThatDoesNotDeclareParameterType(1)))->evaluate(new ValueObjectWithEqualsMethodThatDoesNotDeclareParameterType(1));
    }

    /**
     * @requires PHP 8
     */
    public function testRejectsActualObjectWhenTheSpecifiedMethodHasUnionParameterType(): void
    {
        $this->expectFailure('Parameter of PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatHasUnionParameterType::equals() does not have a declared type.');

        (new ObjectEquals(new ValueObjectWithEqualsMethodThatHasUnionParameterType(1)))->evaluate(new ValueObjectWithEqualsMethodThatHasUnionParameterType(1));
    }

    public function testRejectsActualObjectWhenTheSpecifiedMethodHasIncompatibleParameterType(): void
    {
        $this->expectFailure('PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatHasIncompatibleParameterType is not accepted an accepted argument type for PHPUnit\TestFixture\ObjectEquals\ValueObjectWithEqualsMethodThatHasIncompatibleParameterType::equals().');

        (new ObjectEquals(new ValueObjectWithEqualsMethodThatHasIncompatibleParameterType(1)))->evaluate(new ValueObjectWithEqualsMethodThatHasIncompatibleParameterType(1));
    }

    public function testRejectsActualObjectWhenMethodSaysTheyAreNotEqual(): void
    {
        $this->expectFailure('The objects are not equal according to PHPUnit\TestFixture\ObjectEquals\ValueObject::equals().');

        (new ObjectEquals(new ValueObject(1)))->evaluate(new ValueObject(2));
    }

    private function expectFailure(string $message): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that two objects are equal.' . "\n" . $message);
    }
}
