<?php

/**
 *
 * @author cvancea
 * @package Conny_Messaging
 */
interface Conny_Messaging_Interface
{
    const SUCCESS   = 'success';
    const ERROR     = 'error';
    const ATTENTION = 'attention';
    const INFO      = 'info';

    /**
     * @abstract
     * @param string $message
     */
    function addErrorMessage($message);

    /**
     * @abstract
     * @param array $messages
     */
    function addErrorMessages(array $messages);

    /**
     * @abstract
     * @param string $message
     */
    function addSuccessMessage($message);

    /**
     * @abstract
     * @param array $messages
     */
    function addSuccessMessages(array $messages);

    /**
     * @abstract
     * @param string $message
     */
    function addAttentionMessage($message);

    /**
     * @abstract
     * @param array $messages
     */
    function addAttentionMessages(array $messages);

    /**
     * @abstract
     * @param string $message
     */
    function addInfoMessage($message);

    /**
     * @abstract
     * @param array $messages
     */
    function addInfoMessages(array $messages);
}