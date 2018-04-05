<?php

namespace Selection\Model;

use Selection\Model\Base\SelectionFolder as BaseSelectionFolder;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\UrlRewritingTrait;

class SelectionFolder extends BaseSelectionFolder
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;

    public function getRewrittenUrlViewName()
    {
        return 'selectionFolder';
    }
}
