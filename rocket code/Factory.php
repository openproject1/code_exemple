<?php

/**
 *
 * @author cvancea
 * @package Conny_Messaging
 */
class Conny_Messaging_Factory
{
    /**
     * @return Conny_Messaging_Interface
     */
    public static function getFlashMessenger()
    {
        return Conny_Messaging_FlashMessenger::getInstance();
    }

    /**
     * @return Conny_Messaging_Interface
     */
    public static function getEchoMessenger()
    {
        return new Conny_Messaging_Echo();
    }

    /**
     * @return Conny_Messaging_Interface
     */
    public static function getSilentMessenger()
    {
        return new Conny_Messaging_Array();
    }
}
