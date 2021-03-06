<?php
/**
 *  * Created by PhpStorm.
 * User: Dawid Bednarz( dawid@bednarz.pro )
 */
declare(strict_types=1);

namespace DawBed\ComponentBundle\Command;

use DawBed\ComponentBundle\DependencyInjection\ChildrenBundle\BundleInfo;
use DawBed\ComponentBundle\Model\EventListenerDebuger;
use DawBed\ComponentBundle\Service\ChildrenBundleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EventListenerDebugerCommand extends Command
{
    const NAME = 'dawbed-component:debug:event-listener';

    private $debugCommands;
    private $eventDispatcher;
    private $childrenBundleService;

    public function __construct(
        $name = null,
        array $debugCommands,
        EventDispatcherInterface $eventDispatcher,
        ChildrenBundleService $childrenBundleService
    )
    {
        parent::__construct($name);
        $this->debugCommands = $debugCommands;
        $this->eventDispatcher = $eventDispatcher;
        $this->childrenBundleService = $childrenBundleService;
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Check if you have all registered listeners');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->childrenBundleService->getBundleInfo() as $bundleInfo) {
            (new EventListenerDebuger($this->eventDispatcher, $bundleInfo))
                ->execute($input, $output);
        }
        foreach ($this->debugCommands as $name) {

            $command = $this->getApplication()->find($name);

            $greetInput = new ArrayInput([
                'command' => $name,
            ]);
            $command->run($greetInput, $output);
        }
    }
}