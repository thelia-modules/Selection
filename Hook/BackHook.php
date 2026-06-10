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
    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
            'main.top-menu-tools' => [
                ['type' => 'back', 'method' => 'onMainTopMenuTools'],
            ],
        ];
    }

    public function onModuleConfiguration(\Thelia\Core\Event\Hook\HookRenderEvent $event): void
    {
        $event->add($this->render('Selection/module-configuration.html.twig'));
    }

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
                'url' => URL::getInstance()->absoluteUrl('/admin/selection'),
                'title' => $this->trans('Selections', [], Selection::DOMAIN_NAME)
            ]
        );
    }
}
