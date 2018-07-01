<?php

namespace Edgar\EzTFABundle\EventListener;

use Edgar\EzUIProfile\Menu\Event\ConfigureMenuEvent;
use Edgar\EzUIProfile\Menu\LeftSidebarBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ConfigureMenuListener implements TranslationContainerInterface
{
    const ITEM_PROFILE_TFA = 'user__content__tfa';
    const ITEM_PROFILE_TFA_DESCRIPTION = 'user__content__tfa_description';

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM_PROFILE_TFA,
            [
                'route' => 'edgar.eztfa.menu',
                'extras' => [
                    'icon' => 'lock',
                    'description' => self::ITEM_PROFILE_TFA_DESCRIPTION,
                ],
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
            (new Message(self::ITEM_PROFILE_TFA_DESCRIPTION, 'messages'))->setDesc(
                'Manage Two Factor Authentication with Email/SMS or Yubico Key'
            ),
        ];
    }
}
