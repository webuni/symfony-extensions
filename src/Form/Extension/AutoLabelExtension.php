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

namespace Webuni\SymfonyExtensions\Form\Extension;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\StringUtil;

final class AutoLabelExtension extends AbstractTypeExtension
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['label']) || isset($options['label_format'])) {
            return;
        }

        $actualView = $view;
        $actualForm = $form;
        $names = [];
        do {
            $class = $actualForm->getConfig()->getDataClass();
            if (null !== $class) {
                $names[] = StringUtil::fqcnToBlockPrefix($class);
                break;
            }
            $names[] = $actualView->vars['name'];
        } while (($actualView = $actualView->parent) && ($actualForm = $actualForm->getParent()));

        $view->vars['label'] = Inflector::tableize(implode('.', array_reverse($names)));
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
