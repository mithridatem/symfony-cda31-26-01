<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class WeatherService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly HttpClientInterface $httpClient
    ) {}

    public function getWeather(): array
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?lon=1.44&lat=43.6&appid=" . $this->apiKey . "&units=metric";

        try {
            $response = $this->httpClient->request(
                'GET',
                $url
            );
            return $response->toArray();
        } catch (\Exception $e) {
        }
        return [];
    }

    public function getWeatherCity(string $city): array
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?q=" . $city . "&appid=" . $this->apiKey . "&units=metric";

        try {
            $response = $this->httpClient->request(
                'GET',
                $url
            );
            return $response->toArray();
        } catch (HttpExceptionInterface $e) {
            return ["cod" => $e->getCode()];
        }
    }
}
