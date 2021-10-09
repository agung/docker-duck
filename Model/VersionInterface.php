<?php

namespace Duck\ComposeGenerate\Model;

interface VersionInterface
{
    /**
     * Version of magento
     *
     * @param string $version
     * @return self
     */
    public function version(string $version): self;

    /**
     * Get list version magento
     *
     * @return array
     */
    public function getList(): array;

    /**
     * Get elasticsearch version
     *
     * @return string
     */
    public function getElasticsearch(): string;

    /**
     * Get elasticsearch version
     *
     * @param string $elasticsearch
     * @return void
     */
    public function setElasticsearch(string $elasticsearch): void;

    /**
     * Get mariadb version
     *
     * @return string
     */
    public function getMariadb(): string;

    /**
     * Set mariadb version
     *
     * @param string $mariadb
     */
    public function setMariadb(string $mariadb): void;

    /**
     * Get redis version
     *
     * @return string
     */
    public function getRedis(): string;

    /**
     * Set redis version
     *
     * @param string $redis
     */
    public function setRedis(string $redis): void;

    /**
     * Get php version
     *
     * @return string
     */
    public function getPhp(): string;

    /**
     * Set php version
     *
     * @param string $php
     */
    public function setPhp(string $php): void;
}
