<?php

namespace App\Service\Validator;


class MailValidatorService
{
    const MAIL_SIZE = 255;

    /**
     * Checking sizeof string
     *
     * @param null|string $address
     * @return bool
     */
    public function checkSize(?string $address): bool
    {
        return $address !== null && strlen($address) < self::MAIL_SIZE;
    }

    /**
     * Method check string
     *
     * @param null|string $address
     * @param string $pattern
     * @return bool
     */
    public function pregAddress(?string $address, string $pattern): bool
    {
        return preg_match($pattern, $address);
    }

    /**
     * Check exists in DNS database
     *
     * @param null|string $address
     * @return bool
     */
    public function checkDNS(?string $address): bool
    {
        $host = explode("@", $address);
        if (count($host) !== 2) {
            return false;
        }
        return checkdnsrr($host[1]);
    }

}