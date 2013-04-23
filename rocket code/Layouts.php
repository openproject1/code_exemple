<?php

/**
 * Basic Page Layout Helpers
 *
 * @author cvancea
 */
class Conny_View_Helper_Layouts extends Zend_View_Helper_Abstract
{
    const CLASS_SUCCESS_MSG   = 'success-msg msg';
    const CLASS_ERROR_MSG     = 'error-msg msg';
    const CLASS_ATTENTION_MSG = 'attention-msg msg';
    const CLASS_INFO_MSG      = 'info-msg msg';


    protected $_output;

    /**
     *
     * Zend view helper standard controller function
     * @param string $component
     */
    public function layouts($component) {

        $this->_output = '';

        switch ($component) {
            case 'messages'  : $this->_getMessages(); break;
            default:
        }

        return $this->_output;
    }

    /**
     *
     * Create layout output for the messenges
     */
    protected function _getMessages(){

        // get the messeges object
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        /*
         * Zend standard getCurrentMessages ( use when no redirect is done ) and create output message base on the message type
         */
        if($flashMessenger->setNamespace('success')->hasCurrentMessages()){
            $this->_createMessagesOutput($flashMessenger->getCurrentMessages(), self::CLASS_SUCCESS_MSG);
            $flashMessenger->clearCurrentMessages();
        }
        if($flashMessenger->setNamespace('error')->hasCurrentMessages()){
              $this->_createMessagesOutput($flashMessenger->getCurrentMessages(), self::CLASS_ERROR_MSG);
              $flashMessenger->clearCurrentMessages();
        }
        if($flashMessenger->setNamespace('attention')->hasCurrentMessages()){
            $this->_createMessagesOutput($flashMessenger->getCurrentMessages(), self::CLASS_ATTENTION_MSG);
            $flashMessenger->clearCurrentMessages();
        }
        if($flashMessenger->setNamespace('info')->hasCurrentMessages()){
              $this->_createMessagesOutput($flashMessenger->getCurrentMessages(), self::CLASS_INFO_MSG);
              $flashMessenger->clearCurrentMessages();
        }


    	/*
    	 * Zend standard getMessages ( use when a redirect is done ) and create output message base on the message type
    	 */
        if($flashMessenger->setNamespace('success')->hasMessages()){
            $this->_createMessagesOutput($flashMessenger->getMessages(), self::CLASS_SUCCESS_MSG);
        }
        if($flashMessenger->setNamespace('error')->hasMessages()){
              $this->_createMessagesOutput($flashMessenger->getMessages(), self::CLASS_ERROR_MSG);
        }
        if($flashMessenger->setNamespace('attention')->hasMessages()){
            $this->_createMessagesOutput($flashMessenger->getMessages(), self::CLASS_ATTENTION_MSG);
        }
        if($flashMessenger->setNamespace('info')->hasMessages()){
              $this->_createMessagesOutput($flashMessenger->getMessages(), self::CLASS_INFO_MSG);
        }

    }

     /**
      *
      * Create message html with diferent class wrapper and translate the message
      * @param array $messeges
      * @param string $styleClass
      */
     protected function _createMessagesOutput($messeges, $styleClass){

         // wrapper div
         $this->_output .= '<div class="'.$styleClass.'">';

         foreach ($messeges as $msg){
             if (is_array($msg) && isset($msg['message'])) {
                 $this->_output .= $this->view->translate($msg['message']);
             }
             else {
                 $this->_output .= $this->view->translate($msg);
                 $this->_output .= '<br />';
             }
         }

         // end wraper div
         $this->_output .= '</div>';
     }
}