<?php

namespace Prophecy\Argument\Token;

use Prophecy\Util\StringUtil;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Object state-checker token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ObjectStateToken implements TokenInterface
{
    private $name;
    private $value;
    private $util;

    /**
     * Initializes token.
     *
     * @param string          $methodName
     * @param mixed           $value      Expected return value
     * @param null|StringUtil $util
     */
    public function __construct($methodName, $value, StringUtil $util = null)
    {
        $this->name  = $methodName;
        $this->value = $value;
        $this->util  = $util ?: new StringUtil;
    }

    /**
     * Scores 8 if argument is an object, which method returns expected value.
     *
     * @param mixed $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        if (is_object($argument) && method_exists($argument, $this->name)) {
            $actual = call_user_func(array($argument, $this->name));

            return $actual == $this->value ? 8 : false;
        }

        if (is_object($argument) && property_exists($argument, $this->name)) {
            return $argument->{$this->name} === $this->value ? 8 : false;
        }

        return false;
    }

    /**
     * Returns false.
     *
     * @return bool
     */
    public function isLast()
    {
        return false;
    }

    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('state(%s(), %s)',
            $this->name,
            $this->util->stringify($this->value)
        );
    }
}
