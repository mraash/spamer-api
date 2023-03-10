<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\VkAccount\VkAccountService;
use App\Http\Request\VkAccount\CreateVkAccountInput;
use App\Http\Response\VkAccount\CreationLinkResponse;
use App\Http\Response\VkAccount\VkAccountCreatedResponse;
use App\Http\Response\VkAccount\VkAccountDeletedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VkAccountController extends AbstractController
{
    public function __construct(
        private VkAccountService $vkAccountService,
    ) {
    }

    #[Route('/v1/vk-accounts/link', methods: 'GET', name: 'api.v1.vkAccounts.link')]
    public function link(): JsonResponse
    {
        $link = $this->vkAccountService->getCreationLink();

        return $this->json(new CreationLinkResponse($link));
    }

    #[Route('/v1/vk-accounts/create', methods: 'GET', name: 'api.v1.vkAccounts.create')]
    public function create(CreateVkAccountInput $input): JsonResponse
    {
        $vkId = $input->getUserId();
        $accessToken = $input->getAccessToken();

        $user = $this->getUser();

        $this->vkAccountService->create($user, $vkId, $accessToken);

        return $this->json(new VkAccountCreatedResponse());
    }

    #[Route('/v1/vk-accounts/{id<\d+>}', methods: 'DELETE', name: 'api.v1.vkAccounts.delete')]
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUser();

        $this->vkAccountService->delete($user, $id);

        return $this->json(new VkAccountDeletedResponse());
    }
}
