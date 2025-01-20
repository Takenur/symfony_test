<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WebHelper
{
    public static function proxy(string $url,string $endpoint='',string $method="GET",array $params = null,bool $isHtml=true) : array|string
    {
        $client=HttpClient::create();

        $options = [];

        if ($params) {
            if (strtoupper($method) === 'GET') {
                $options['query'] = $params;
            } else {
                $options['json'] = $params;
            }
        }

        try {
            $response = $client->request(strtoupper($method), $url . $endpoint, $options);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                if ($isHtml){
                 return $response->getContent();
                }


                return $response->toArray();
            }

            return [
                'success' => false,
                'status' => $response->getStatusCode(),
                'message' => $response->getContent(false),
            ];
        }
        catch (TransportExceptionInterface $e) {
            return [
                'success' => false,
                'message' => 'Transport error: ' . $e->getMessage(),
            ];
        }
        catch (ClientExceptionInterface $e) {
            return [
                'success' => false,
                'message' => 'Client error: ' . $e->getMessage(),
            ];
        }
        catch (ServerExceptionInterface $e) {
            return [
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ];
        }
        catch (RedirectionExceptionInterface $e) {
            return [
                'success' => false,
                'message' => 'Redirection error: ' . $e->getMessage(),
            ];
        }
        catch (\Exception $exception){
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
        catch (DecodingExceptionInterface $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function checkProductUrl(string $url):bool
    {
        $pattern = '/^https:\/\/world\.openfoodfacts\.org\/product\/\d+\/[\w\-]+$/';
        return preg_match($pattern, $url) === 1;
    }
}