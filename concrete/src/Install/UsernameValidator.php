<?php

namespace Concrete\Core\Install;

use ArrayAccess;

/**
 * Username format rules used during site install, when uniqueness cannot be checked.
 */
class UsernameValidator
{
    /**
     * @param string|mixed $username
     *
     * @return bool
     */
    public function isValid($username, ?ArrayAccess $error = null)
    {
        $username = (string) $username;
        $config = app('config');
        $valid = true;
        $length = strlen($username);
        $minimumLength = (int) $config->get('concrete.user.username.minimum', 1);
        $maximumLength = (int) $config->get('concrete.user.username.maximum');

        if ($minimumLength && $maximumLength && ($length < $minimumLength || $length > $maximumLength)) {
            $valid = false;
            if ($error) {
                $error[] = t('A username must be between %s and %s characters long.', $minimumLength, $maximumLength);
            }
        } elseif ($minimumLength && $length < $minimumLength) {
            $valid = false;
            if ($error) {
                $error[] = t('A username must be at least %s characters long.', $minimumLength);
            }
        } elseif ($maximumLength && $length > $maximumLength) {
            $valid = false;
            if ($error) {
                $error[] = t('A username can be at most %s characters long.', $maximumLength);
            }
        }

        $rxBoundary = '[' . $config->get('concrete.user.username.allowed_characters.boundary') . ']';
        $rxMiddle = '[' . $config->get('concrete.user.username.allowed_characters.middle') . ']';
        $pattern = "/^({$rxBoundary}({$rxMiddle}*{$rxBoundary})?)?$/";
        if (!preg_match($pattern, $username)) {
            $valid = false;
            if ($error) {
                $error[] = t($config->get('concrete.user.username.allowed_characters.error_string'));
            }
        }

        return $valid;
    }
}
