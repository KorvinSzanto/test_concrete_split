<?php

namespace Concrete\Core\Job\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Job\Job;
use Concrete\Core\Job\JobQueueMessage;
use Concrete\Core\Job\QueueableJob;
use League\Tactician\Bernard\QueueableCommand;

class ExecuteJobItemCommandHandler
{

    public function handle(ExecuteJobItemCommand $command)
    {
        /**
         * @var $job QueueableJob
         */
        $job = Job::getByHandle($command->getJobHandle());
        $message = new JobQueueMessage(unserialize($command->getData()));
        $job->processQueueItem($message);

        $queue = $job->getQueueObject();
        if ($queue->getQueue()->count() == 0) {
            $result = $job->finish($queue);
            $job->markCompleted(0, $result);
        }
    }

}