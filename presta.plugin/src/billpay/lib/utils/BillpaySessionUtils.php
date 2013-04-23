<?php

/**
 * Billpay
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Billpay module to newer versions in the future.
 *
 * @category  Payment
 * @package   Billpay Prestashop module
 * @author    Billpay GmbH ( support@billpay.de )
 * @author    Catalin Vancea ( catalin.vancea@billpay.de )
 * @copyright Copyright 2012 Billpay GmbH
 * @license   Commercial
 * @link      https://www.billpay.de/
 */

class BillpaySessionUtils
{
	/**
	 * singleton instance
	 */
	protected static $_instance = null;

	/**
	 * get oxSession object instance (create if needed)
	 *
	 * @return oxSession
	 */
	public static function getInstance()
	{
		if (self::$_instance === null)
			self::$_instance  = new BillpaySessionUtils();

		return self::$_instance;
	}

	/**
	 *
	 * @return void
	 */
	public function startSession()
	{
		if(session_id() == '')
			@session_start();
	}

    /**
     * On success returns true
     *
     * @param string $name
     * @return bool
     */
    public static function hasSessionVar($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     *
     * @param string $name
     * @param mixed  $value
     * @return void
     */
    public static function setSessionVar($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     *
     * @param string $name
     * @return string|null
     */
    public static function getSessionVar($name)
    {
        if ($_SESSION[$name])
            return $_SESSION[$name];

        return;
    }

    /**
     *
     * @param string $name
     * @return void
     */
    public static function deleteSessionVar($name)
    {
        $_SESSION[$name] = null;
        unset($_SESSION[$name]);
    }

    /**
     *
     * @return void
     */
    public static function setSessionBillpayProfilingTagCount()
    {self::deleteSessionVar('billpayProfilingTagCount');
    	if (self::hasSessionVar('billpayProfilingTagCount') === false)
    		self::setSessionVar('billpayProfilingTagCount', 1);
    	else{
    		$billpayProfilingTagCount = (int) self::getSessionVar('billpayProfilingTagCount') + 1;
    		self::setSessionVar('billpayProfilingTagCount', $billpayProfilingTagCount);
    	}
    }

    /**
     *
     * @return string
     */
    public static function getMd5SessionIdAsProfilingTag()
    {
        return md5 (session_id());
    }

    /**
     *
     * @retun void
     */
    public static function setSessionMd5IdToTplSessionId()
    {
		self::setSessionVar('md5id', self::getMd5SessionIdAsProfilingTag());
    }
}