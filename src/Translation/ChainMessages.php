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

class ChainMessages implements \IteratorAggregate
{
    private $messages;

    public function __construct(array $messages = [])
    {
        $this->messages = array_filter($messages);
    }

    public function __toString(): string
    {
        $last = end($this->messages);
        if (\is_array($last)) {
            return (string) current($last);
        }

        return (string) $last;
    }

    public function getIterator()
    {
        return new \ArrayObject($this->messages);
    }
}
