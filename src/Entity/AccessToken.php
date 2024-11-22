<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    public const ACCESS_TOKEN_TTL = 60 * 60; // Access token is valid for 1 hour
    public const REFRESH_TOKEN_TTL = 180 * 24 * 60 * 60; // Refresh token is valid for 180 days

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $refreshToken = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'accessTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $refreshTokenUsed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->createdAt->getTimestamp() + self::ACCESS_TOKEN_TTL > time();
    }

    public function isRefreshTokenValid(): bool
    {
        return $this->createdAt->getTimestamp() + self::REFRESH_TOKEN_TTL > time() && $this->refreshTokenUsed === false;
    }

    public function isRefreshTokenUsed(): ?bool
    {
        return $this->refreshTokenUsed;
    }

    public function setRefreshTokenUsed(bool $refreshTokenUsed): static
    {
        $this->refreshTokenUsed = $refreshTokenUsed;

        return $this;
    }

    public function toJson(): array
    {
        return [
            'access_token' => [
                'token' => $this->accessToken,
                'expiration' => $this->createdAt->getTimestamp() + self::ACCESS_TOKEN_TTL
            ],
            'refresh_token' => $this->refreshToken,
            'user' => $this->user->toJson()
        ];
    }

}
