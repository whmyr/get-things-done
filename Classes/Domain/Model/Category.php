<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Category extends AbstractEntity
{
    protected string $title;

    protected ?string $description = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Category
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Category
    {
        $this->description = $description;
        return $this;
    }
}
