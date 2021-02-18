<?php

namespace Serfhos\PowermailFromOverride\Signal;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use In2code\Powermail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SignalSlot: Extend SendMailService functions
 *
 * @package Serfhos\PowermailFromOverride\Signal
 */
class SendMailServiceSignal {

    /**
     * Do our magic before email is sent!
     *
     * @param MailMessage     $message
     * @param array           $email
     * @param SendMailService $sendMailService
     *
     * @return void
     */
    public function beforeSend(MailMessage $message, array $email, SendMailService $sendMailService) {
        $settings = $sendMailService->getSettings();
        if ((bool)$settings['moveFromToReplyTo']['enabled'] === TRUE) {

            // >= TYPO3 10
            if (
                class_exists('TYPO3\CMS\Core\Information\Typo3Version') &&
                GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 10
            ) {
                if (
                    !is_array($message->getReplyTo()) ||
                    $message->getReplyTo()[0]->getAddress() === $message->getFrom()[0]->getAddress()
                ) {
                    $staticFromAddress = $this->getConfiguredFromSettings($settings);
                    $message->setReplyTo([$message->getFrom()[0]]);
                    $message->setFrom(array_key_first($staticFromAddress), $staticFromAddress[array_key_first($staticFromAddress)]);
                }
                else {
                    $this->log('Moving of From => ReplyTo is disabled. Reply-To already set, check if <type>.overwrite.replyToEmail is set.', $message->getReplyTo());
                }
                // <= TYPO3 9
            }
            else {
                if (empty($message->getReplyTo()) || $message->getReplyTo() === $message->getFrom()) {
                    $message->setReplyTo($message->getFrom());
                    $message->setFrom($this->getConfiguredFromSettings($settings));
                }
                else {
                    $this->log('Moving of From => ReplyTo is disabled. Reply-To already set, check if <type>.overwrite.replyToEmail is set.', $message->getReplyTo());
                }
            }
        }
    }

    /**
     * Get configured or default 'email from' setting
     *
     * @param array $settings
     *
     * @return array
     */
    protected function getConfiguredFromSettings($settings) {
        $from = (array)$settings['moveFromToReplyTo']['from'];
        $name = ($from['name']) ? $from['name'] : ConfigurationUtility::getDefaultMailFromName();
        $email = ($from['email']) ? $from['email'] : ConfigurationUtility::getDefaultMailFromAddress();
        return array($email => $name);
    }

    /**
     * Log message in Default Log Manager
     *
     * @param string $message
     * @param array  $data
     * @param int    $severity
     */
    protected function log($message, array $data = array(), $severity = \TYPO3\CMS\Core\Log\LogLevel::NOTICE) {
        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        $logger->log($severity, $message, $data);
    }
}
