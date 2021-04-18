<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use App\Service\SettingsService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController
{
    private $settingsRepository;
    private $settingsService;

    public function __construct(SettingsRepository $settingsRepository, SettingsService $settingsService)
    {
        $this->settingsRepository = $settingsRepository;
        $this->settingsService = $settingsService;
    }

    /**
     * @Route("/api", name="add_settings", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data['id']    = $request->request->get('id');
        $data['field'] = $request->request->get('field');
        $data['value'] = $request->request->get('value');
        $data['type']  = $request->request->get('type');

        $this->validateRequest($data['type'] ?? null);

        $result = $this->settingsService->addSettings($data['type'], $data);

        if ($redirect = $request->get('_target_path')) {
            return new RedirectResponse($redirect);
        }

        return $result;
    }

    /**
     * @Route("/api/{type}", name="get_settings_by_type", methods={"GET"})
     */
    public function get(?string $type = null)
    {
        $this->validateRequest($type);

        return $this->settingsService->getSettings($type);
    }

    /**
     * @Route("/api/{id}", name="update_settings", methods={"PUT"})
     */
    public function update(Request $request)
    {
        $id = $request->get('id');
        $data['field'] = $request->request->get('field');
        $data['value'] = $request->request->get('value');
        $data['type']  = $request->request->get('type');

        $this->validateRequest($data['type'] ?? null);

        $result = $this->settingsService->updateSettings($id, $data);

        if ($redirect = $request->get('_target_path')) {
            return new RedirectResponse($redirect);
        }

        return $result;
    }

    private function validateRequest(?string $type = null): void
    {
        if ($type && ! in_array($type, Settings::ALLOWED_TYPES)) {
            throw new NotFoundHttpException(sprintf('Type %s is not a valid settings type!', $type));
        }
    }
}