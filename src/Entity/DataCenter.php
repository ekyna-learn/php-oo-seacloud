<?php

declare(strict_types=1);

namespace Entity;

class DataCenter
{
    private ?int    $id   = null;
    private ?string $name = null;
    private ?string $code = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): DataCenter
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): DataCenter
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): DataCenter
    {
        $this->code = $code;

        return $this;
    }
}
