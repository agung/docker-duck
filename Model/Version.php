<?php

namespace Duck\ComposeGenerate\Model;

class Version
{
    protected string $elasticsearch;
    protected string $mariadb;
    protected string $redis;
    protected string $php;

    /**
     * Magento version
     *
     * @param string $version
     * @return self
     */
    public function version(string $version): self
    {
        // elasticsearch version 6 not supported for arm
        switch ($version) {
            case '2.3.4':
                $this->elasticsearch = '6.8.18';
                $this->mariadb = '10.2';
                $this->redis = '5.0';
                $this->php = '7.2';
                break;
            case '2.4.0':
                $this->elasticsearch = '7.6.2';
                $this->mariadb = '10.2';
                $this->redis = '5.0';
                $this->php = '7.4';
                break;
            case '2.4.2':
            case '2.4.3':
                $this->elasticsearch = '7.9.0';
                $this->mariadb = '10.4';
                $this->redis = '6.0';
                $this->php = '7.4';
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * Get list magento version
     *
     * @return array
     */
    public function getList(): array
    {
        return [
            '2.3.4',
            '2.4.0',
            '2.4.2',
            '2.4.3'
        ];
    }

    /**
     * Get elasticsearch version
     *
     * @return string
     */
    public function getElasticsearch(): string
    {
        return $this->elasticsearch;
    }

    /**
     * Get elasticsearch version
     *
     * @param string $elasticsearch
     * @return void
     */
    public function setElasticsearch(string $elasticsearch): void
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Get mariadb version
     *
     * @return string
     */
    public function getMariadb(): string
    {
        return $this->mariadb;
    }

    /**
     * Set mariadb version
     *
     * @param string $mariadb
     */
    public function setMariadb(string $mariadb): void
    {
        $this->mariadb = $mariadb;
    }

    /**
     * Get redis version
     *
     * @return string
     */
    public function getRedis(): string
    {
        return $this->redis;
    }

    /**
     * Set redis version
     *
     * @param string $redis
     */
    public function setRedis(string $redis): void
    {
        $this->redis = $redis;
    }

    /**
     * Get php version
     *
     * @return string
     */
    public function getPhp(): string
    {
        return $this->php;
    }

    /**
     * Set php version
     *
     * @param string $php
     */
    public function setPhp(string $php): void
    {
        $this->php = $php;
    }
}
