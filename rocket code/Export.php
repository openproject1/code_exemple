<?php

/**
 * Order_Model_Csv_Export
 *
 * @author cvancea
 * @package Order_Model
 */
class Order_Model_Csv_Export extends Order_Model_Csv
{
    /**
     * File handle
     * @var resource
     */
    protected $_handle;

    /**
     * @var obj
     */
    protected $_orderServiceOrder;

    /**
     * Data written in the csv file
     * @var array
     */
    protected $_data = array();


    /**
     * Optional: The data can be set on initialization
     * @param array
     */
    public function __construct($aData = array())
    {
        $this->setData($aData);
    }

	/**
     * @param $_orderServiceOrder
     */
    public function setOrderServiceOrder($oOrderServiceOrder)
    {
        $this->_orderServiceOrder = $oOrderServiceOrder;
    }

    /**
     * Get instance of a service
     * @return Order_Service_Order (OrderExt_Service_Order)
     */
    protected function getOrderServiceOrder()
    {
        if (null === $this->_orderServiceOrder) {
            $this->_orderServiceOrder = new OrderExt_Service_Order();
        }
        return $this->_orderServiceOrder;
    }

    /**
     * Set the data that will be written to the csv file
     *
     * @param  array $aData
     * @return Order_Model_Csv_Export
     */
    public function setData(array $aData)
    {
        $this->_data = $aData;
        return $this;
    }

	/**
     * @return void
     */
    protected function _clearData()
    {
        $this->_data = array();
    }

    /**
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Write orders to physical file on the path
     * @param $sFileName string
     * @param $sFilePath string
     * @return int|bool
     */
    public function writeCsvFile($sFileName = null, $sFilePath = null)
    {
        Conny_Utils_File_Helpers::createPath($sFilePath);

        // open physical file
        $this->_handle = fopen($sFilePath . $sFileName, 'w+');

        $this->_getCsvHeader();
        $this->_writeCsvHeader();

        $this->_transformDataForCsv();
        $mWriteResult = $this->_writeCsvData();

        fclose($this->_handle);

    	return $mWriteResult;
    }

    /**
     *  Opens a file
     *  @param $sFileName string
     *  @return void
     */
    public function openCsvFile($sFileName)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . (string) $sFileName . '"');

        // open write-only stream file handel
        $this->_handle = fopen('php://output', 'w');

        $this->_getCsvHeader();
        $this->_writeCsvHeader();

        $this->_transformDataForCsv();
        $this->_writeCsvData();

        fclose($this->_handle);
    }

	/**
     * writes the header in to the csv file
     */
    protected function _writeCsvHeader()
    {
        fputcsv($this->_handle, $this->_csvHeader, $this->_delimiter, $this->_enclosure);
    }


    /**
     * Writes data in to the csv file
     * @return int|bool
     */
    protected function _writeCsvData()
    {
        // TODO return a better array result after write line by line
        $mResult = false;

        foreach ($this->_data as $aRow){
            $mResult = fputcsv($this->_handle, $aRow, $this->_subDelimiter, $this->_enclosure);
            if ($mResult === false) break;
        }

        return $mResult;
    }

    /**
     * Set the data array that will be writen in the file
     * @return void
     */
    protected function _transformDataForCsv()
    {
       // no data just return
       if (empty($this->_data)){
           return $this->_data;
       }

       // set data local variable for data manipulation and clear the data parameter
       $aOrders = $this->getData();
       $this->_clearData();

       // get parcel tracking
       $parcelTracking = $this->getOrderServiceOrder()->getParcelTracking();

       // if array is a single order transform this array in a multi array ( used in the foreach )
       $test = array_key_exists('id_sales_order', $aOrders);
       if(true === $test) {
           $aOrders = array($aOrders);
       }

       foreach ($aOrders as $order) {

            foreach ($order['item_collection'] as $item) {
                if (!empty($item['pending_status']['carrier'])) {
                    /** @var $parcelTrackingCarrier Transfer_Parcel_Tracking */
                    $parcelTrackingCarrier = $parcelTracking[$item['pending_status']['carrier']];
                    $parcelTrackingCarrierName = $parcelTrackingCarrier->getCarrierName();
                } else {
                    $parcelTrackingCarrierName = '';
                }

                $gender = $order['customer']['gender'];
                if(!isset( $item['address_delivery']['company'])) {
                     $item['address_delivery']['company'] = null;
                } else {
                    $gender = null;
                }
                if(!isset( $order['address_shipping']['middle_name'])) {
                    $item['address_delivery']['middle_name'] = null;
                }
                $row = array(
                    $order['created_at'],
                    $order['order_nr'],
                    $item['id_sales_order_item'],
                    $item['address_delivery']['company'],
                    (!empty($gender) ? $this->_getPrefix($gender) : ''),
                    $item['address_delivery']['first_name'] ,
                    $item['address_delivery']['middle_name'],
                    $item['address_delivery']['last_name'],
                    $item['address_delivery']['address1'],
                    $item['address_delivery']['address2'],
                    $item['address_delivery']['postcode'],
                    $item['address_delivery']['city'],
                    $item['sku_supplier_simple'],
                    $item['sku'],
                    $item['name'],
                    $item['unit_price'],
                    $order['shipping_amount'],
                    (!empty($item['brand_name']) ? $item['brand_name'] : ''),
                    (!empty($item['properties']) ? $this->_getProprieties($item['properties']) : ''),
                    (!empty($item['pending_status']['pending_status_view']) ? $item['pending_status']['pending_status_view'] : $item['status_view']),
                    $parcelTrackingCarrierName,
                    (!empty($item['pending_status']['slip_number']) ? $item['pending_status']['slip_number'] : ''),
                    (!empty($item['pending_status']['pending_note']) ? $item['pending_status']['pending_note'] : ''),
                );

                // write back in the parameter
                $this->_data[] = $row;
            }
        }
    }

    /**
     * Get the gender returns prefix(salutation)
     * @param $sPrefix string
     * @return string
     */
    private function _getPrefix($sPrefix){
        $sSalutation = '';
        if (strlen((string) $sPrefix)) {
            if ($sPrefix == 'male') {
                $sSalutation = 'Mr.';
            } elseif ($sPrefix == 'female') {
                $sSalutation = 'Mrs.';
            }
        }
        return $sSalutation;
    }

    /**
     * get proprieties as string
     * @param array $aProperties
     * @internal param string $sPrefix
     * @return string
     */
    private function _getProprieties($aProperties){
        $sProperties = '';
        if (!empty($aProperties)) {
            foreach ($aProperties as $Key => $Value) {
                $sProperties .= $Key.': '.$Value.',';
            }
            $sProperties = substr($sProperties, 0, -1);
        }

        return $sProperties;
    }
}