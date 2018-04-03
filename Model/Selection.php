<?php

namespace Selection\Model;

use Selection\Model\Base\Selection as BaseSelection;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\UrlRewritingTrait;

class Selection extends BaseSelection
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;

    public function getRewrittenUrlViewName()
    {
        return 'selection';
    }
}
