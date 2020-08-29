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
use Doctrine\Inflector\InflectorFactory;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AutoLabelExtension extends AbstractTypeExtension
{
    public const AUTO_LABEL_STOP_ON_DATA_CLASS = 'auto_label_stop_on_data_class';

    private $inflector;

    public function __construct()
    {
        $this->inflector = class_exists(InflectorFactory::class) ? InflectorFactory::create()->build() : new Inflector();
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['label']) || isset($options['label_format'])) {
            return;
        }

        $actualView = $view;
        $actualForm = $form;
        $names = [];
        do {
            $config = $actualForm->getConfig();
            $class = $config->getDataClass();
            if (null !== $class) {
                $names[] = StringUtil::fqcnToBlockPrefix($class);
                if ($config->getOption(self::AUTO_LABEL_STOP_ON_DATA_CLASS, true)) {
                    break;
                }
            } elseif (isset($actualView->vars['name'])) {
                $names[] = $actualView->vars['name'];
            }
        } while (($actualView = $actualView->parent) && ($actualForm = $actualForm->getParent()));

        if (!empty($names)) {
            $view->vars['label'] = $this->inflector->tableize(implode('.', array_reverse($names)));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            self::AUTO_LABEL_STOP_ON_DATA_CLASS => true,
        ]);
        $resolver->setAllowedTypes(self::AUTO_LABEL_STOP_ON_DATA_CLASS, 'bool');
    }

    public function getExtendedType(): string
    {
        return FormType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
