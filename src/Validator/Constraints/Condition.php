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

use Symfony\Component\Validator\Constraints\Composite;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class Condition extends Composite
{
    public $condition;
    public $on;
    public $constraints = [];

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'condition';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return [
            'condition',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompositeOption()
    {
        return 'constraints';
    }
}
