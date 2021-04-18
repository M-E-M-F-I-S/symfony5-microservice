<?php

namespace App\Service;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SettingsService
{
    private $grpcClient;
    private $restClient;
    private $httpClient;
    private $settingsRepository;

    public function __construct(HttpClientInterface $httpClient, SettingsRepository $settingsRepository)
    {
        $this->httpClient = $httpClient;
        $this->settingsRepository = $settingsRepository;
    }

    public function addSettings(string $type, array $data): JsonResponse
    {
        foreach ($data as $field => $value) {
            if (empty($type) || empty($field) || empty($value)) {
                throw new NotFoundHttpException('Expecting mandatory parameters!');
            }
            $newSettings = new Settings();
            $newSettings->setType($type)
                ->setField($field)
                ->setValue($value);

            $this->settingsRepository->save($newSettings);
        }

        return new JsonResponse(['status' => 'Settings saved!'], Response::HTTP_CREATED);
    }

    public function updateSettings(int $id, array $data): JsonResponse
    {
        if (! $settings = $this->settingsRepository->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException(sprintf('Not found row with id %s!', $id));
        }

        $settings->setType($data['type']);
        $settings->setField($data['field']);
        $settings->setValue($data['value']);

        $this->settingsRepository->save($settings);

        return new JsonResponse(['status' => 'Settings saved!'], Response::HTTP_OK);
    }

    public function getSettings(?string $type = null): JsonResponse
    {
        $result = [];
        switch ($type) {
            case Settings::TYPE_REST:
                $response = $this->restClient->request(
                    'http://remote.microservice/settings'
                );

                $result = $response->toArray();
                foreach ($result as $data) {
                    $result[] = [
                        'id'    => $data['id'],
                        'field' => $data['field'],
                        'value' => $data['value'],
                    ];
                }
                break;

            case Settings::TYPE_RPC:
                $response = $this->grpcClient->request(
                    'http://remote.microservice/settings'
                );

                $result = $response->toArray();
                foreach ($result as $data) {
                    $result[] = [
                        'id'    => $data['id'],
                        'field' => $data['field'],
                        'value' => $data['value'],
                    ];
                }
                break;

            case Settings::TYPE_HTTP:
                $response = $this->httpClient->request(
                    'GET',
                    'http://remote.microservice/settings'
                );

                $result = $response->toArray();
                foreach ($result as $data) {
                    $result[] = [
                        'id'    => $data['id'],
                        'field' => $data['field'],
                        'value' => $data['value'],
                    ];
                }
                break;

            default:
                if (! $response = $this->settingsRepository->findAll()) {
                    throw new NotFoundHttpException('No settings found!');
                }

                foreach ($response as $data) {
                    $result[] = [
                        'id'    => $data->getId(),
                        'type'  => $data->getType(),
                        'field' => $data->getField(),
                        'value' => $data->getValue(),
                    ];
                }
                break;
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }
}