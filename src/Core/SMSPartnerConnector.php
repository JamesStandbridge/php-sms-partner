<?php

namespace Jamesstandbridge\PhpSmsPartner\Core;

use Jamesstandbridge\PhpSmsPartner\Core\SMSPartnerConstants;
use Jamesstandbridge\PhpSmsPartner\Core\SMSPartnerApiException;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;

class SMSPartnerConnector
{
    private $client;
    private $api_key;

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
        $this->client = new Client();
    }

    public function listAllContacts(string $groupId): array 
    {
        $total = [];
        $page = 1;
        do {
            try {
                $res = $this->get(SMSPartnerConstants::LIST_GROUP_CONTACTS($this->api_key, $page, $groupId));

                $content = self::decode($res);

                $total = array_merge($total, $content["datas"]);
    
                $left = $content["total"] - (count($content["datas"]) + (($page - 1) * SMSPartnerConstants::CONTACTS_PER_PAGE));
                $page++;
            } catch(ClientException $e) {
                throw new SMSPartnerApiException($e->getResponse()->getBody()->getContents());
            }
        } while($left > 0);

        return $total;
    }

    public function addContact(string $groupId, string $phonenumber, array $data): array
    {   
        try {
            $payload = [
                "apiKey" => $this->api_key,
                "groupId" => $groupId,
                "contact" => array(
                    "phoneNumber" => $phonenumber,
                    "firstname" => $data["firstname"],
                    "lastname" => $data["lastname"],
                    "date" => $data["date"] ? $data["date"]->format("Y-m-d") : null,
                    "shortUrlPartnr" => $data["shortUrlPartnr"],
                    "url" => $data["url"],
                    "custom1" => $data["custom1"],
                    "custom2" => $data["custom2"],
                    "custom3" => $data["custom3"],
                    "custom4" => $data["custom4"]
                )
            ];
            $res = $this->post(SMSPartnerConstants::POST_CONTACT(), $payload);       
            $customer = self::decode($res);

            return $customer;
        } catch(ClientException $e) {
            throw new SMSPartnerApiException($e->getResponse()->getBody()->getContents());
        }
        return null;
    }

    public function deleteContact(string $contactId): bool
    {
        try {
            $payload = [
                "apiKey" => $this->api_key,
                "contactId" => $contactId
            ];
            $res = $this->post(SMSPartnerConstants::DELETE_CONTACT(), $payload);

            $res = self::decode($res);

            return $res["code"] === 200;
        } catch(ClientException $e) {
            throw new SMSPartnerApiException($e->getResponse()->getBody()->getContents());
        }
        return false;
    }

    public function addStop(string $phoneNumber): int
    {
        try {
            $payload = [
                "apiKey" => $this->api_key,
                "phoneNumber" => $phoneNumber
            ];
            $res = $this->post(SMSPartnerConstants::POST_STOP(), $payload);

            $res = self::decode($res);

            return $res["stopId"];
        } catch(ClientException $e) {
            throw new SMSPartnerApiException($e->getResponse()->getBody()->getContents());
        }
        return null;   
    }

    public function deleteStop(int $stopId): bool
    {
        try {
            $res = $this->get(SMSPartnerConstants::DELETE_STOP($this->api_key, $stopId));
            $res = self::decode($res);

            return $res["success"];
        } catch(ClientException $e) {
            throw new SMSPartnerApiException($e->getResponse()->getBody()->getContents());
        }
        return false;  
    }

    public function listStops(): array
    {
        try {
            $res = $this->get(SMSPartnerConstants::LIST_STOPS($this->api_key));

            $res = self::decode($res);

            return $res["data"];
        } catch(ClientException $e) {
            throw new SMSPartnerApiException($e->getResponse()->getBody()->getContents());
        }
        return false; 
    }

    private static function decode(Response $res) 
    {
        return json_decode($res->getBody()->getContents(), true);
    }

    private function get(string $url): Response
    {
        return $this->client->request("GET", $url);
    }

    private function post(string $url, array $payload): Response
    {
        return $this->client->request("POST", $url, ["body" => json_encode($payload)]);
    }   
}