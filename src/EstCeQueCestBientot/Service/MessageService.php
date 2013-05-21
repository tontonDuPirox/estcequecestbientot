<?php

namespace EstCeQueCestBientot\Service;

use EstCeQueCestBientot\Model\Message;
use EstCeQueCestBientot\Exception\MessageNotFoundException;
use EstCeQueCestBientot\Service\ConfigurationService;

/**
 * Service handling messages
 */
class MessageService
{

    /**
     * @var \EstCeQueCestBientot\Service\ConfigurationService 
     */
    private $configurationService;

    /**
     * @param \EstCeQueCestBientot\Service\ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService) {
        $this->configurationService = $configurationService;
    }

    /**
     * Fetching messages from the Yaml file
     * @return array
     */
    public function fetchAll() {
        $messages = array();
        $messagesFromFile = $this->configurationService->getMessages();
        foreach ($messagesFromFile as $messageFromFile) {
            $message = new Message();
            $message->setMessage($messageFromFile['message'])
                    ->setStart($messageFromFile['startHour'], $messageFromFile['startMinute'])
                    ->setEnd($messageFromFile['endHour'], $messageFromFile['endMinute'])
                    ->setItsTime($messageFromFile['itsTime']);
            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * @param \DateTime $dateTime
     * @return Message
     * @throws MessageNotFoundException
     */
    public function getMessageAt(\DateTime $dateTime) {
        $messages = $this->fetchAll();
        $message = null;

        if (!empty($messages)) {
            foreach ($messages as $msg) {
                if ($msg->getStart() <= $dateTime && $dateTime <= $msg->getEnd()) {
                    $message = $msg;
                    break;
                }
            }
        }
        
        if ($message === null) {
            throw new MessageNotFoundException();
        }

        return $message;
    }

}