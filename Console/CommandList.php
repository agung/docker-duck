<?php

namespace Duck\ComposeGenerate\Console;

use Duck\ComposeGenerate\Console\Command\Install;
use Magento\Framework\Console\CommandListInterface;
use Magento\Framework\ObjectManagerInterface;

class CommandList implements CommandListInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Gets list of command classes
     *
     * @return string[]
     */
    private function getCommandsClasses(): array
    {
        return [ Install::class ];
    }

    /**
     * @inheritdoc
     */
    public function getCommands(): array
    {
        $commands = [];
        foreach ($this->getCommandsClasses() as $class) {
            if (class_exists($class)) {
                $commands[] = $this->objectManager->get($class);
            } else {
                throw new \RuntimeException('Class ' . $class . ' does not exist');
            }
        }

        return $commands;
    }
}
