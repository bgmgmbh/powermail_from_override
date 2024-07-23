<?php

namespace Serfhos\PowermailFromOverride\EventListener;

use In2code\Powermail\Events\SendMailServicePrepareAndSendEvent;
use In2code\Powermail\Utility\ConfigurationUtility;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SignalSlot: Extend SendMailService functions
 *
 * @package Serfhos\PowermailFromOverride\Signal
 */
class SendMailServiceEventListener {
    /**
     * Do our magic before email is sent!
     */
    public function __invoke(SendMailServicePrepareAndSendEvent $event) : void
    {
        $message = $event->getMailMessage();
        $sendMailService = $event->getSendMailService();

        $settings = $sendMailService->getSettings();

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($settings['moveFromToReplyTo']);

        if ((bool)$settings['moveFromToReplyTo']['enabled'] === true) {
            die('aa');
            if (!is_array($message->getReplyTo()) ||
                $message->getReplyTo()[0]->getAddress() === $message->getFrom()[0]->getAddress()
            ) {
                $staticFromAddress = $this->getConfiguredFromSettings($settings);
                $message->setReplyTo([$message->getFrom()[0]]);
                $message->setFrom(array_key_first($staticFromAddress), $staticFromAddress[array_key_first($staticFromAddress)]);
            }
            else {
                $this->log('Moving of From => ReplyTo is disabled. Reply-To already set, check if <type>.overwrite.replyToEmail is set.', $message->getReplyTo());
            }
        }

        die();

        $event->setMailMessage($message);
    }

    /**
     * Get configured or default 'email from' setting
     */
    protected function getConfiguredFromSettings(array $settings) : array
    {
        $from = $settings['moveFromToReplyTo']['from'] ?? [];
        $name = $from['name'] ?: ConfigurationUtility::getDefaultMailFromName();
        $email = $from['email'] ?: ConfigurationUtility::getDefaultMailFromAddress();
        return array($email => $name);
    }

    /**
     * Log message in Default Log Manager
     */
    protected function log(string $message, array $data = [], string $severity = LogLevel::NOTICE) {
        /** @var $logger Logger */
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $logger->log($severity, $message, $data);
    }
}
