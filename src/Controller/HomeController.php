<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $settingService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingService = $settingsService;
    }

    /**
     * @Route("/", name="default", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'data' => null
        ]);
    }

    /**
     * @Route("/load/{type}", name="load_settings_by_type", methods={"GET"})
     */
    public function load(?string $type = null): Response
    {
        $data = null;
        if ($type === 'MySQL' || in_array($type,Settings::ALLOWED_TYPES)) {
            $data = $this->settingService->getSettings($type)->getContent();
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'type' => $type,
            'data' => $data ? json_decode($data) : null
        ]);
    }
}
