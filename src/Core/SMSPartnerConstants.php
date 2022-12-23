<?php

namespace Jamesstandbridge\PhpSmsPartner\Core;


class SMSPartnerConstants
{
    const CONTACTS_PER_PAGE = 500;
    const DOMAIN = "https://api.smspartner.fr/v1";
    
    public static function LIST_GROUP_CONTACTS(string $apikey, int $page, string $groupId): string
    {
        return sprintf("%s/contact/list?apiKey=%s&groupId=%s&page=%s", 
            self::DOMAIN,
            $apikey, 
            $groupId, 
            $page
        );
    }

    public static function LIST_STOPS(string $apiKey): string
    {
        return sprintf("%s/stop-sms/list?apiKey=%s", self::DOMAIN, $apiKey);
    }

    public static function POST_CONTACT(): string
    {
        return sprintf("%s/contact/add", self::DOMAIN);
    }

    public static function UPDATE_CONTACT(): string
    {
        return sprintf("%s/contact/update", self::DOMAIN);
    }

    public static function DELETE_CONTACT(): string
    {
        return sprintf("%s/contact/delete", self::DOMAIN);
    }

    public static function POST_STOP(): string
    {
        return sprintf("%s/stop-sms/add", self::DOMAIN);
    }

    public static function DELETE_STOP(string $apiKey, int $stopId): string 
    {
        return sprintf("%s/stop-sms/delete?apiKey=%s&id=%s", self::DOMAIN, $apiKey, $stopId);
    }
}