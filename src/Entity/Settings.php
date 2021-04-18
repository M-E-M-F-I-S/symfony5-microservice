<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings
{
    public const TYPE_REST = 'RestAPI';
    public const TYPE_RPC  = 'gRPC';
    public const TYPE_HTTP = 'http';
    public const ALLOWED_TYPES = [
        self::TYPE_REST,
        self::TYPE_RPC,
        self::TYPE_HTTP
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $field;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    public function __construct(string $type, string $field, string $value)
    {
        $this->validate($type);
        $this->type = $type;
        $this->field = $field;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->validate($type);
        $this->type = $type;

        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    private function validate(string $type) : void
    {
        if (! in_array($type, self::ALLOWED_TYPES)) {
            throw new Exception(sprintf('Type %s is not a valid settings type!', $type));
        }
    }
}
