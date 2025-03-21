<?php

namespace plugins\NovaPoshta\classes\base;

/**
 * Class Options
 * @package plugins\NovaPoshta\classes\base
 *
 * @property int locationsLastUpdateDate
 * @property array shippingMethodSettings
 * @property string senderArea
 * @property string senderCity
 * @property string senderWarehouse
 * @property string apiKey
 * @property bool useFixedPriceOnDelivery
 * @property float fixedPrice
 * @property bool pluginRated
 *
 */
class Options extends Base
{
    const AREA_NAME = 'area_name';
    const AREA = 'area';
    const CITY_NAME = 'city_name';
    const CITY = 'city';
    const WAREHOUSE_NAME = 'warehouse_name';
    const WAREHOUSE = 'warehouse';
    const API_KEY = 'api_key';
    const DEBUG = 'debug';
    const USE_FIXED_PRICE_ON_DELIVERY = 'use_fixed_price_on_delivery';
    const USE_SHIPPING_PRICE_ON_DELIVERY = 'use_shipping_price_on_delivery';
    const FIXED_PRICE = 'fixed_price';
    const OPTION_CASH_ON_DELIVERY = 'on_delivery';
    const OPTION_FIXED_PRICE = 'fixed_price';
    const OPTION_PLUGIN_RATED = 'plugin_rated';

    const FREE_SHIPPING_MIN_SUM = 'free_shipping_min_sum';
    const FREE_SHIPPING_TEXT = 'free_shipping_text';    


    /**
     * @return float
     */
    protected function getFreeShippingMinSum()
    {
        return $this->shippingMethodSettings ? (float)$this->shippingMethodSettings[self::FREE_SHIPPING_MIN_SUM] : 0.00;
    }

    /**
     * @return text
     */
    protected function getUseFreeShippingText()
    {
        return $this->useFreeShippingText ? $this->shippingMethodSettings[self::FREE_SHIPPING_MIN_TEXT] : '';
    }  

    /**
     * @return void
     */
    public function ajaxPluginRate()
    {
        NPttn()->log->info('Plugin marked as rated');
        $this->setOption(self::OPTION_PLUGIN_RATED, 1);
        $result = array(
            'result' => true,
            'message' => __('Thank you :)', NOVA_POSHTA_TTN_DOMAIN)
        );
        echo json_encode($result);
        exit;
    }

    /**
     * @return bool
     */
    protected function getUseFixedPriceOnDelivery()
    {
        return filter_var($this->shippingMethodSettings[self::USE_FIXED_PRICE_ON_DELIVERY], FILTER_SANITIZE_STRING);
    }

    /**
     * @return float
     */
    protected function getFixedPrice()
    {
        return $this->useFixedPriceOnDelivery ? (float)$this->shippingMethodSettings[self::FIXED_PRICE] : null;
    }

    /**
     * @return int
     */
    public function getLocationsLastUpdateDate()
    {
        return $this->getOption('locations_last_update_date') ?: 0;
    }

    /**
     * @param int $value
     */
    public function setLocationsLastUpdateDate($value)
    {
        $this->setOption('locations_last_update_date', $value);
        $this->locationsLastUpdateDate = $value;
    }

    /**
     * @return array
     */
    protected function getShippingMethodSettings()
    {
        return get_site_option('woocommerce_nova_poshta_shipping_method_settings');
    }

    /**
     * @return string
     */
    protected function getSenderArea()
    {
        return $this->shippingMethodSettings ? $this->shippingMethodSettings[self::AREA] : '';
    }

    /**
     * @return string
     */
    protected function getSenderCity()
    {
        return $this->shippingMethodSettings ? $this->shippingMethodSettings[self::CITY] : '';
    }

    /**
     * @return string
     */
    protected function getSenderWarehouse()
    {
        return $this->shippingMethodSettings ? $this->shippingMethodSettings[self::WAREHOUSE] : '';
    }

    protected function getPluginRated()
    {
        return filter_var($this->getOption(self::OPTION_PLUGIN_RATED), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return string
     */
    protected function getApiKey()
    {
        return get_option('mrkvnp_sender_api_key');
        //deprecated
        //return $this->shippingMethodSettings[self::API_KEY];
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return false;
    }

    /**
     * Delete all plugin specific options from options table
     * @return void
     */
    public function clearOptions()
    {
        $table = NPttn()->db->options;
        $query = "DELETE FROM `$table` WHERE option_name LIKE CONCAT ('_nova_poshta_', '%')";
        NPttn()->db->query($query);
    }

    /**
     * @param $optionName
     * @return mixed
     */
    private function getOption($optionName)
    {
        $key = "_nova_poshta_" . $optionName;
        return get_option($key);
    }

    /**
     * @param string $optionName
     * @param mixed $optionValue
     */
    private function setOption($optionName, $optionValue)
    {
        $key = "_nova_poshta_" . $optionName;
        update_option($key, $optionValue);
    }

    /**
     * @var Options
     */
    private static $_instance;

    /**
     * @return Options
     */
    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Options constructor.
     *
     * @access private
     */
    private function __construct()
    {
    }

    /**
     * @access private
     */
    private function __clone()
    {
    }

}
