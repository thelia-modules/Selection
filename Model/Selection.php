<?php

namespace Selection\Model;

use Selection\Model\Base\Selection as BaseSelection;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\UrlRewritingTrait;
use Thelia\Model\Tools\PositionManagementTrait;

class Selection extends BaseSelection
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;
    use PositionManagementTrait;

    public function getRewrittenUrlViewName()
    {
        return 'selection';
    }
}
