<?php

namespace Edgar\EzTFABundle\EventListener;

use Edgar\EzUIProfile\Menu\Event\ConfigureMenuEvent;
use Edgar\EzUIProfile\Menu\LeftSidebarBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ConfigureMenuListener implements TranslationContainerInterface
{
    const ITEM_PROFILE_TFA = 'user__content__tfa';

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $cronsMenu = $menu->getChild(LeftSidebarBuilder::ITEM__PASSWORD);
        $cronsMenu->getParent()->addChild(
            self::ITEM_PROFILE_TFA,
            [
                'route' => 'edgar.eztfa.menu',
                'extras' => ['icon' => 'lock'],
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_PROFILE_TFA, 'messages'))->setDesc('Two Factor Authentication'),
        ];
    }
}
