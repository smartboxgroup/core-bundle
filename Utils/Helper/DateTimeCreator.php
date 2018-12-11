<?php

namespace Smartbox\CoreBundle\Utils\Helper;

class DateTimeCreator
{
    /**
     * Using the same strategy as Monolog does for creating time stamps with micro seconds.
     *
     * microtime without \ is used as it we need to be able to ClockMock it.
     *
     * @return \DateTime
     */
    public static function getNowDateTime()
    {
        if (PHP_VERSION_ID < 70100) {
            $now = \DateTime::createFromFormat('U.u', \sprintf('%.6F', microtime(true)));
        } else {
            $now = new \DateTime(null);
        }

        return $now;
    }
}
