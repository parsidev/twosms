<?php
namespace Parsidev\TwoSms;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Parsidev\TwoSms\Enums\Method;

class TwoSms
{
    const BASE_URL = "https://rest.payamak-panel.com/api/SendSMS/";

    protected string $username;
    protected string $password;
    protected string $fromNum;
    protected Client $client;

    public function __construct(string $username, string $password, string $fromNumber)
    {
        $this->username = $username;
        $this->password = $password;
        $this->fromNum = $fromNumber;
        $this->client = new Client([
            'base_uri' => self::BASE_URL
        ]);
    }

    /**
     * @throws Exception
     */
    protected function send(string $uri, array $data, Method $method): mixed
    {
        try {
            $response = $this->client->request($method->value, $uri, ["form_params" => $data]);
            $body = $response->getBody();
            return json_decode($body->getContents());
        } catch (GuzzleException $e) {
            throw new Exception(sprintf("TwoSms: %s", $e->getMessage()));
        }
    }

    /**
     * @throws Exception
     */
    public function SendSMS($to, $message, $isFlash = false)
    {
        if (is_array($to)) {
            $to = implode(",", $to);
        }
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'from' => $this->fromNum,
            'to' => $to,
            'text' => $message,
            'isFlash' => $isFlash
        ];

        return $this->send('SendSMS', $data, Method::POST);
    }

    /**
     * @throws Exception
     */
    public function GetDelivery($recID)
    {
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'recID' => $recID
        ];

        return $this->send('GetDeliveries2', $data, Method::POST);
    }

    public function GetCredit()
    {
        $data = [
            'username' => $this->username,
            'password' => $this->password
        ];

        return $this->send('GetCredit', $data, Method::POST);
    }

}

