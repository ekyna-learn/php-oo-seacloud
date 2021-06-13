<?php

declare(strict_types=1);

namespace Entity;

class Server
{
    public const STATE_PENDING = 0;
    public const STATE_STOPPED = 1;
    public const STATE_READY   = 2;

    private ?int          $id           = null;
    private ?User         $user         = null;
    private ?DataCenter   $location     = null;
    private ?Distribution $distribution = null;
    private ?string       $name         = null;
    private ?string       $ip           = null;
    private int           $state        = self::STATE_PENDING;
    private int           $cpu          = 1;
    private int           $ram          = 1;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Server
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Server
    {
        $this->user = $user;

        return $this;
    }

    public function getLocation(): ?DataCenter
    {
        return $this->location;
    }

    public function setLocation(DataCenter $location): Server
    {
        $this->location = $location;

        return $this;
    }

    public function getDistribution(): ?Distribution
    {
        return $this->distribution;
    }

    public function setDistribution(Distribution $distribution): Server
    {
        $this->distribution = $distribution;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Server
    {
        $this->name = $name;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): Server
    {
        $this->ip = $ip;

        return $this;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): Server
    {
        $this->state = $state;

        return $this;
    }

    public function getCpu(): int
    {
        return $this->cpu;
    }

    public function setCpu(int $cpu): Server
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getRam(): int
    {
        return $this->ram;
    }

    public function setRam(int $ram): Server
    {
        $this->ram = $ram;

        return $this;
    }
}
