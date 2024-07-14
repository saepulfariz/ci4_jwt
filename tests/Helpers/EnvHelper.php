<?php

namespace Tests\Helpers;

class EnvHelper
{
    public static function setEnvVariable($key, $value)
    {
        // use
        // EnvHelper::setEnvVariable('refreshToken', $refreshToken);
        $path = realpath(__DIR__ . '/../../.env');

        if (file_exists($path)) {
            file_put_contents($path, preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                file_get_contents($path)
            ));
        } else {
            file_put_contents($path, "{$key}={$value}\n", FILE_APPEND);
        }
    }
}
