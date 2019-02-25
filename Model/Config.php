<?php
/**
 * Created by PhpStorm.
 * User: bjoern
 * Date: 2018-11-29
 * Time: 11:30
 */

namespace Phoenix\MediaStorageSync\Model;


use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return true if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            $this->getXmlPathPrefix() . 'enabled'
        );
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        $url = $this->scopeConfig->getValue(
            $this->getXmlPathPrefix() . 'url'
        );
        if (substr($url, -1) == '/') {
            $url = substr($url, 0, -1);
        }
        return $url;
    }

    /**
     * Get HTTP client user
     *
     * @return string
     */
    public function getHttpClientUser()
    {
        return $this->scopeConfig->getValue(
            $this->getXmlPathPrefix() . 'http_client_user'
        );
    }

    /**
     * Get HTTP client password
     *
     * @return string
     */
    public function getHttpClientPassword()
    {
        return $this->scopeConfig->getValue(
            $this->getXmlPathPrefix() . 'http_client_password'
        );
    }

    /**
     * Return the configuration path prefix
     *
     * @return string
     */
    protected function getXmlPathPrefix()
    {
        return 'phoenix_mediastoragesync/general/';
    }
}