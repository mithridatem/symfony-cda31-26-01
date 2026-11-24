<?php

namespace App\Controller;

use App\Form\WeatherType;
use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherController extends AbstractController
{
    public function __construct(
        private readonly WeatherService $weather
    ) {}

    #[Route('/weather', name: 'app_weather')]
    public function index(): Response
    {
        dd($this->weather->getWeather());

        return $this->render('weather/index.html.twig', [
            'controller_name' => 'WeatherController',
        ]);
    }

    #[Route(path: '/weather/city', name: 'app_weather_city', methods: ['GET', 'POST'])]
    public function weatherCity(Request $request): Response
    {
        $form = $this->createForm(WeatherType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //Récupération de la ville
            $city = $form->get('city')->getData();
            //Récupération des données Météo
            $data = $this->weather->getWeatherCity($city);
        }

        return $this->render('weather/weather_city.html.twig', [
            'form' => $form,
            'weather' => $data ?? null
        ]);
    }
}
