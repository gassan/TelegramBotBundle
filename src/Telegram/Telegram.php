<?php

/*
 * This file is part of the BoShurikTelegramBotBundle.
 *
 * (c) Alexander Borisov <boshurik@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BoShurik\TelegramBotBundle\Telegram;

use BoShurik\TelegramBotBundle\Event\UpdateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

/*final*/ class Telegram
{
    public function __construct(private BotApi $api, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function processUpdates(): void
    {
        $updates = $this->api->getUpdates();

        $lastUpdateId = null;
        foreach ($updates as $update) {
            $lastUpdateId = $update->getUpdateId();
            $this->processUpdate($update);
        }

        if ($lastUpdateId) {
            $this->api->getUpdates($lastUpdateId + 1, 1);
        }
    }

    public function processUpdate(Update $update)
    {
        $event = new UpdateEvent($update);
        $this->eventDispatcher->dispatch($event);
    }
}
