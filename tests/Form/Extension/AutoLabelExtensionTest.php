<?php

namespace Webuni\SymfonyExtensions\Tests\Form\Extension;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormView;
use Webuni\SymfonyExtensions\Form\Extension\AutoLabelExtension;

class AutoLabelExtensionTest extends TestCase
{
    private $factory;
    private $extension;

    protected function setUp(): void
    {
        $this->factory = (new FormFactoryBuilder())->addTypeExtension(new AutoLabelExtension())->getFormFactory();
        $this->extension = new AutoLabelExtension();
    }

    public function testExtendsForm()
    {
        $this->assertEquals(FormType::class, $this->extension->getExtendedType()); // Symfony 3
        $this->assertEquals([FormType::class], AutoLabelExtension::getExtendedTypes());
    }

    public function testPreserveLabel()
    {
        $view = new FormView();
        $form = $this->createMock(Form::class);

        $this->extension->finishView($view, $form, ['label' => 'Custom label']);
        $this->assertArrayNotHasKey('label', $view->vars);

        $this->extension->finishView($view, $form, ['label_format' => 'Custom label format']);
        $this->assertArrayNotHasKey('label', $view->vars);
    }

    public function testNotReplaceLabelForFormWithoutNecessaryData(): void
    {
        $form = $this->factory->create();
        $view = $form->createView();

        $this->assertArrayHasKey('label', $view->vars);
        $this->assertEquals('form', $view->vars['label']);
    }

    public function testAutoLabelFromView()
    {
        $form = $this->factory->createNamedBuilder('foo')->add(
            $this->factory->createNamedBuilder('Bar')->add('test')
        )->getForm();

        $this->assertSetLabel('foo.bar.test', $form->get('Bar')->get('test'));
    }

    public function testAutoLabelFromDataClass()
    {
        $form = $this->factory->createNamedBuilder('foo')->add(
            $this->factory->createNamedBuilder('bar', FormType::class, new DataClassTest(), ['data_class' => DataClassTest::class])
                ->add('inner')
        )->getForm();

        $this->assertSetLabel('data_class_test.inner', $form->get('bar')->get('inner'));
    }

    public function testAutoLabelFromDataClassWithoutReset()
    {
        $form = $this->factory->createNamedBuilder('foo')->add(
            $this->factory->createNamedBuilder('bar', FormType::class, new DataClassTest(), [
                'data_class' => DataClassTest::class,
                AutoLabelExtension::AUTO_LABEL_STOP_ON_DATA_CLASS => false,
            ])
                ->add('inner')
        )->getForm();

        $this->assertSetLabel('foo.data_class_test.inner', $form->get('bar')->get('inner'));
    }

    private function assertSetLabel(string $label, Form $form)
    {
        $view = $form->createView();
        $this->assertArrayHasKey('label', $view->vars);
        $this->assertEquals($label, $view->vars['label']);
    }
}

class DataClassTest
{
    public $inner;
}
