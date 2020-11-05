<?php

namespace Concrete\Core\Messenger\Batch\Command;

use Concrete\Core\Foundation\Command\Command;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface BatchProcessMessageInterface
{

    /**
     * Returns the UUID of the batch process this message belongs to.
     *
     * @return string
     */
    public function getBatchProcess(): string;


}
