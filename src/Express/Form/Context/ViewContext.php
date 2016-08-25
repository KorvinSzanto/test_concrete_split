<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Entity\Express\Control\Control;

class ViewContext extends Context
{

    public function getControlRenderer(Control $control)
    {
        return $control->getViewControlRenderer();
    }


}
