<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\Summary\Field;
use Doctrine\Common\Collections\ArrayCollection;

interface FieldInterface
{

    const FIELD_TITLE = 'title';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_THUMBNAIL = 'thumbnail';
    const FIELD_LINK = 'link';
    const FIELD_DATE = 'date';
    const FIELD_DATE_COMPLEX = 'date_complex';

    public function getFieldIdentifier();

}
