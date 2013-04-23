<?php

/**
 * @author cvancea
 * @package Conny_Messaging
 */
class Conny_Messaging_FlashMessenger extends Conny_Messaging_Abstract implements Conny_Messaging_Interface
{
    private static $_instance;

    /**
     * @static
     * @return Conny_Messaging
     */
    static public function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // @var Zend_Controller_Action_Helper_FlashMessenger
    protected $_messenger;

    /**
     *
     */
    protected function __construct()
    {
        $this->_messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * @param string $message
     */
    public function addErrorMessage($message)
    {
        if(strlen($message['message'])){
            $this->_messenger->setNamespace(Conny_Messaging_Interface::ERROR)->addMessage($message);
        }
    }

    /**
     * @param string $message
     */
    public function addSuccessMessage($message)
    {
        if(strlen($message['message'])){
            $this->_messenger->setNamespace(Conny_Messaging_Interface::SUCCESS)->addMessage($message);
        }
    }

    /**
     * @param string $message
     */
    public function addAttentionMessage($message)
    {
        if(strlen($message['message'])){
            $this->_messenger->setNamespace(Conny_Messaging_Interface::ATTENTION)->addMessage($message);
        }
    }

    /**
     * @param string $message
     */
    public function addInfoMessage($message)
    {
        if(strlen($message['message'])){
            $this->_messenger->setNamespace(Conny_Messaging_Interface::INFO)->addMessage($message);
        }
    }
}