<?php

require_once('config.php');
require_once(AUTOLOAD_PATH);

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

putenv("WEBDRIVER_CHROME_DRIVER=" . CHROMEDRIVER_PATH);

class Browser
{
    private static ChromeDriver $driver;
    private static ChromeOptions $options;
    private static DesiredCapabilities $capabilities;

    static function setOptions() : void
    {
        self::$options = new ChromeOptions();
        self::$options->setBinary(CHROME_PATH);
        self::$options->addArguments(['start-maximized']);
        self::$options->addArguments(['-headless']);
    }

    static function setCapabilites() : void
    {
        self::$capabilities = DesiredCapabilities::chrome();
        self::$capabilities->setCapability(ChromeOptions::CAPABILITY, self::$options);
    }

    static function setting(): void {
        self::setOptions();
        self::setCapabilites();
    }

    static function getDriver(?string $url = null) : ChromeDriver
    {
        self::setting();

        self::$driver = ChromeDriver::start(self::$capabilities);

        if ($url) {
            self::$driver->get($url);
            sleep(3);
        }
        return self::$driver;
    }
}




