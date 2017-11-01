<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Description;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Workflow;
use Concrete\Package\Calendar\Workflow\Progress\CalendarEventProgress;
use HtmlObject\Element;
use Concrete\Core\Calendar\Event\EventService;

class UnapproveCalendarEventRequest extends CalendarEventRequest
{

    public function __construct()
    {
        $pk = Key::getByHandle('approve_calendar_event');
        parent::__construct($pk);
    }

    public function trigger()
    {
        $event = $this->getRequestedEventVersionObject()->getEvent();
        $calendar = $event->getCalendar();
        $pk = $this->getWorkflowRequestPermissionKeyObject();
        $pk->setPermissionObject($calendar);

        return parent::triggerRequest($pk);
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new Description();
        $event = $this->getRequestedEventVersionObject()->getEvent();
        if (is_object($event)) {
            // Completely new page.
            $d->setEmailDescription(t("Event unapproval requested for event \"%s\".",
                $event->getName()));
            $d->setDescription(t("Event: %s", $event->getName()));
            $d->setInContextDescription(t("Event %s submitted for unapproval.", $event->getName()));
            $d->setShortStatus(t("Event Unapproval"));
        }

        return $d;
    }

    public function getWorkflowRequestStyleClass()
    {
        return 'info';
    }

    public function getWorkflowRequestApproveButtonClass()
    {
        return '';
    }

    public function getWorkflowRequestApproveButtonInnerButtonRightHTML()
    {
        return '<i class="fa fa-thumbs-o-up"></i>';
    }

    public function getWorkflowRequestApproveButtonText()
    {
        return t('Unapprove');
    }

    public function getRequestIconElement()
    {
        $span = new Element('i');
        $span->addClass('fa fa-file');

        return $span;
    }

    public function addWorkflowProgress(Workflow $wf)
    {
        $pwp = CalendarEventProgress::add($wf, $this);
        $r = $pwp->start();
        $pwp->setWorkflowProgressResponseObject($r);

        return $pwp;
    }

    public function approve(Progress $wp)
    {
        $event = $this->getRequestedEventVersionObject()->getEvent();
        $service = \Core::make(EventService::class);
        if ($event) {
            $service->unapprove($event);
        }

        $wpr = new Response();

        return $wpr;
    }

}
