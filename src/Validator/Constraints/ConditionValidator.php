<?php

declare(strict_types=1);

/*
 * This is part of the webuni/symfony-extensions package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\SymfonyExtensions\Validator\Constraints;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConditionValidator extends ConstraintValidator
{
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Condition) {
            throw new UnexpectedTypeException($constraint, Condition::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $comparedValue = $value;
        if ($path = $constraint->on) {
            if (null === $object = $this->context->getRoot()) {
                return;
            }

            if ($path instanceof FormBuilder || $path instanceof Form) {
                $path = '['.$path->getName().'].data';
            }

            try {
                $comparedValue = $this->propertyAccessor->getValue($object, $path);
            } catch (NoSuchPropertyException $e) {
                throw new ConstraintDefinitionException(sprintf('Invalid property path "%s" provided to "%s" constraint: %s', $path, \get_class($constraint), $e->getMessage()), 0, $e);
            }
        }

        $context = $this->context;
        $conditionViolations = $context->getValidator()->validate($comparedValue, $constraint->condition);
        if (0 !== \count($conditionViolations)) {
            return;
        }

        $context->getValidator()->inContext($context)->validate($value, $constraint->constraints);
    }
}
