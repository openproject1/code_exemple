<?php

/**
 *
 * @author cvancea
 * @package Conny_Messaging
 */
abstract class Conny_Messaging_Abstract implements Conny_Messaging_Interface
{
    /**
     * @param array $messages
     */
    public function addSuccessMessages(array $messages)
    {
        foreach($messages as $message) {
            $this->addSuccessMessage($message);
        }
    }

    /**
     * @param array $messages
     */
    public function addErrorMessages(array $messages)
    {
        foreach($messages as $message) {
            $this->addErrorMessage($message);
        }
    }

    /**
     * @param array $messages
     */
    public function addAttentionMessages(array $messages)
    {
        foreach($messages as $message) {
            $this->addAttentionMessage($message);
        }
    }

    /**
     * @param array $messages
     */
    public function addInfoMessages(array $messages)
    {
        foreach($messages as $message) {
            $this->addInfoMessage($message);
        }
    }
}