<?php

declare(strict_types=1);

namespace Whmyr\Taskmanager\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Whmyr\Taskmanager\Domain\Repository\CategoryRepository;

class CategoryService implements SingletonInterface
{
    private array $settings;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        ConfigurationManagerInterface $configurationManager,
    ) {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
        );
    }

    public function getAllCategories(): QueryResultInterface
    {
        $pid = (int)$this->settings['taskCategoriesStoragePid'] ?: 0;

        return $this->categoryRepository->findBy(['pid' => $pid]);
    }
}
