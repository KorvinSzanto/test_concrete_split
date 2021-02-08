<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Scheduler\Scheduler;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Command\Task\TaskSetService;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Notification\Mercure\MercureService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Punic\Comparer;

class Tasks extends DashboardPageController
{
    public function view()
    {
        $taskSets = $this->app->make(TaskSetService::class)->getGroupedTasks();
        $mercureService = $this->app->make(MercureService::class);
        $eventSource = null;
        $poll = false;
        if ($mercureService->isEnabled()) {
            $eventSource = $mercureService->getPublisherUrl();
        } else {
            $poll = true;
        }
        $this->set('poll', $poll);
        $this->set('pollToken', $this->token->generate('poll'));
        $this->set('eventSource', $eventSource);
        $this->set('taskSets', $taskSets);
        $this->set('schedulingEnabled', $this->app->make(Scheduler::class)->isEnabled());

    }
}
