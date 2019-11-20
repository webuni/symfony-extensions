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

namespace Webuni\SymfonyExtensions\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlipArrayType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setNormalizer('choices', function ($resolver, $data) {
            return array_flip($data);
        });
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
