<?php

declare(strict_types=1);

namespace App\Domain\Service\VkAccount;

use App\Domain\Entity\User;
use App\Domain\Entity\VkAccount;
use App\Domain\Repository\VkAccountRepository;
use App\Domain\Service\VkAccount\Exception\VkAccountAlreadyAdded;
use App\Integration\VkApi\VkApiInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VkAccountService
{
    public function __construct(
        private VkApiInterface $vkApi,
        private VkAccountRepository $repository,
        private UrlGeneratorInterface $urlGenerator,
        private bool $isDev,
        private int $vkAppId,
    ) {
    }

    public function getCreationLink(): string
    {
        $redirectUrl = $this->urlGenerator->generate('api.vkAccounts.create', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($this->isDev) {
            $redirectUrl = null;
        }

        return $this->vkApi->getAuthLink($this->vkAppId, $redirectUrl);
    }

    public function create(User $owner, int $vkId, string $vkAccessToken): VkAccount
    {
        if ($this->repository->findByVkId($owner, $vkId) !== null) {
            throw new VkAccountAlreadyAdded();
        }

        $vkAccount = new VkAccount();

        $vkAccount
            ->setOwner($owner)
            ->setVkId($vkId)
            ->setVkAccessToken($vkAccessToken)
        ;

        $this->repository->save($vkAccount);
        $this->repository->flush();

        return $vkAccount;
    }
}
