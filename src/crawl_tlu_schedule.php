<?php

require_once("browser.php");

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverSelect;

class SaveCookie
{
    public static $url = "http://dangky.tlu.edu.vn/CMCSoft.IU.Web.info/login.aspx";
    public $user, $pass;
    public ChromeDriver $browser;

    function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;

        $this->browser = Browser::getDriver(self::$url);
    }

    function getAccountId(){
        return [
            'userId'=>'txtUserName',
            'passId'=>'txtPassword',
            'btnName'=>'btnSubmit'
        ];
    }

    function mapping() {

        $IDs = $this->getAccountId();
        
        $userElement = $this->browser->findElement(WebDriverBy::id($IDs['userId']));
        $passElement = $this->browser->findElement(WebDriverBy::id($IDs['passId']));
        $btnElement = $this->browser->findElement(WebDriverBy::name($IDs['btnName']));

        return[
            'userElement'=>$userElement,
            'passElement'=>$passElement,
            'btnElement'=>$btnElement
        ];
    }

    function login() {
        $elements = $this->mapping();

        $elements['userElement']->sendKeys($this->user);
        $elements['passElement']->sendKeys($this->pass);
        $elements['btnElement']->click();

        sleep(2);
    }

    function save_cookie($filename = "") {
        $this->login();

        $cookies = $this->browser->manage()->getCookies();

        $this->browser->quit();

        $this->sendDatatoJsonFile("my_cookie.json", $cookies);
    }

    function sendDatatoJsonFile($filename = "", $data) {
        $jsonFile = fopen($filename, 'w');
        foreach($data as $key=>$value) {
            $jsonData[] = [
                'name'=>$value->getName(),
                'value'=>$value->getValue()
            ];
        }
        $jsonData = json_encode($jsonData);
        fwrite($jsonFile, $jsonData);
    }
}

class CrawlSchedule{

    const URL = "http://dangky.tlu.edu.vn/CMCSoft.IU.Web.Info/Reports/Form/StudentTimeTable.aspx";
    public ChromeDriver $browser;

    function __construct()
    {
        $this->browser = Browser::getDriver(CrawlSchedule::URL);
    }

    function loadCookie() {
        $file = fopen("my_cookie.json", "r");
        $cookies = json_decode(fread($file, filesize("my_cookie.json")));

        foreach($cookies as $cookie) {
            $this->browser->manage()->addCookie(new Cookie($cookie->name, $cookie->value));
        }

        $this->browser->get(CrawlSchedule::URL);

        sleep(2);
    }

    function getSubjectXpath($count) {
        return [
            'name'=>"//*[@id='gridRegistered']/tbody/tr[$count]/td[2]",
            'time'=>"//*[@id='gridRegistered']/tbody/tr[$count]/td[4]",
            'classroom'=>"//*[@id='gridRegistered']/tbody/tr[$count]/td[5]"
        ];
    }

    function mapping($count) {
        $xpaths = $this->getSubjectXpath($count);

        $nameElement = $this->browser->findElement(WebDriverBy::xpath($xpaths['name']));
        $timeElement = $this->browser->findElement(WebDriverBy::xpath($xpaths['time']));
        $classroomElement = $this->browser->findElement(WebDriverBy::xpath($xpaths['classroom']));

        return [
            'name'=>$nameElement,
            'time'=>$timeElement,
            'classroom'=>$classroomElement,
        ];
    }

    function getSubject($count) {
        $elements = $this->mapping($count);

        $name = $elements['name']->getText();
        $time = $elements['time']->getText();
        $classroom = $elements['classroom']->getText();

        return [
            'name'=>$name,
            'time'=>$time,
            'classroom'=>$classroom
        ];
    }

    function getSchedule() {
        $this->loadCookie();

        $this->choseSchoolYear();

        $schedule = [];

        $length = (int) $this->browser->findElement(WebDriverBy::cssSelector("#gridRegistered > tbody > tr:nth-last-child(2) > td:nth-child(1)"))->getText();

        $count = 2;

        for($i=2;$i<=$length+1;$i++) {
            $subject = $this->getSubject($count);
            $schedule[] = $subject;
        }

        $this->browser->quit();

        return $schedule;
    }

    function choseSchoolYear(string $schoolYear = "1_2021_2022") {
        $selectElement = $this->browser->findElement(WebDriverBy::name('drpSemester'));
        $select = new WebDriverSelect($selectElement);

        $select->selectByVisibleText($schoolYear);
        sleep(2);
    }

    function convertDatatoJsonFile($filename, $data) {
        $file = fopen($filename, "w");
        fwrite($file, json_encode($data));
        fclose($file);
    }
}


$a = new CrawlSchedule();
$data = $a->getSchedule();
print_r($data);
$a->convertDatatoJsonFile("schedule.json", $data);





