<?php

namespace Selection\Hook;

use Selection\Selection;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;

/***
 * Class BackHook
 * @package Selection\Hook
 * @author Maxime Bruchet <mbruchet@openstudio.fr>
 */
class BackHook extends BaseHook
{
    /***
     * Hook Selection module to the sidebar in tools menu
     *
     * @param HookRenderBlockEvent $event
     */
    public function onMainTopMenuTools(HookRenderBlockEvent $event)
    {
        $event->add(
            [
                'id' => 'tools_menu_selection',
                'class' => '',
                'url' => URL::getInstance()->absoluteUrl('/admin/Selection'),
                'title' => $this->trans('Selection', [], Selection::DOMAIN_NAME)
            ]
        );
    }
}
