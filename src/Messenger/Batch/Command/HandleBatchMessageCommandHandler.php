<?php

namespace Concrete\Core\Messenger\Batch\Command;

use Concrete\Core\Entity\Messenger\BatchProcess;
use Symfony\Component\Messenger\MessageBusInterface;

class HandleBatchMessageCommandHandler
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(HandleBatchMessageCommand $command)
    {
        $message = $command->getMessage();
        return $this->messageBus->dispatch($message);
    }


}
