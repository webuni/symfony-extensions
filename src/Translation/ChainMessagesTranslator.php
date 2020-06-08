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

namespace Webuni\SymfonyExtensions\Translation;

use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

if (class_exists(LegacyTranslatorInterface::class) && !class_exists(TranslatorInterface::class)) {
    abstract class BaseChainMessagesTranslator implements LegacyTranslatorInterface, TranslatorBagInterface
    {
    }
} elseif (class_exists(LegacyTranslatorInterface::class)) {
    abstract class BaseChainMessagesTranslator implements LegacyTranslatorInterface, TranslatorInterface, TranslatorBagInterface
    {
    }
} else {
    abstract class BaseChainMessagesTranslator implements TranslatorInterface, LocaleAwareInterface, TranslatorBagInterface
    {
    }
}

class ChainMessagesTranslator extends BaseChainMessagesTranslator
{
    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     * @param string|object $id
     */
    public function trans($id, array $parameters = [], string $domain = null, string $locale = null)
    {
        if ($id instanceof ChainMessages) {
            return $this->transChain($id, $parameters, $domain, $locale);
        }

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @inheritDoc
     * @param string|object $id
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        if ($id instanceof ChainMessages) {
            return $this->transChoiceChain($id, $number, $parameters, $domain, $locale);
        }

        return $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
    }

    public function setLocale($locale)
    {
        if ($this->translator instanceof LocaleAwareInterface || $this->translator instanceof LegacyTranslatorInterface) {
            return $this->translator->setLocale($locale);
        }
    }

    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    public function getCatalogue(string $locale = null)
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            return $this->translator->getCatalogue($locale);
        }
    }

    private function transChain(ChainMessages $id, $parameters, $domain, $locale)
    {
        foreach ($id as $candidate) {
            $result = $this->translator->trans(\is_array($candidate) ? current($candidate) : $candidate, $parameters, \is_array($candidate) ? key($candidate) : $domain, $locale);
            if ($result !== $candidate) {
                return $result;
            }
        }

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    private function transChoiceChain(ChainMessages $id, $number, $parameters, $domain, $locale)
    {
        foreach ($id as $candidate) {
            if (\is_array($candidate)) {
                $candidate = current($candidate);
                $domain = key($candidate);
            }

            $result = $this->translator->transChoice(\is_array($candidate) ? current($candidate) : $candidate, $number, $parameters, \is_array($candidate) ? key($candidate) : $domain, $locale);
            if ($result !== $candidate) {
                return $result;
            }
        }

        return $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
    }
}
