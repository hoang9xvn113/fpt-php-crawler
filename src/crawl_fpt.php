<?php

require_once("browser.php");

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;

class CrawlFpt
{
    public $currScroll = 600;

    private ChromeDriver $browser;

    public $phoneSelector;

    function __construct(?string $url = null)
    {
        $this->browser = Browser::getDriver($url);
    }

    function scrollPage()
    {
        for($i=0;$i<10;$i++) {
            $this->currScroll += 500;
            $this->browser->executeScript("window.scrollTo(0, {$this->currScroll})");
            sleep(1);
        }
    }

    function seemore() {
        $btnSelector = "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product--loadmore > a";
        $btnElement = $this->browser->findElement(WebDriverBy::cssSelector($btnSelector));
        $btnElement->click();
    }

    function crawl($action, $quantity = 24): array
    {
        $data = [];

        $count = 1;

        $this->scrollPage();

        while (true) {
            if ($count > $quantity) break;

            if ($count%25==0) {
                $this->seemore();
                sleep(2);
                $this->scrollPage();
            }

            try {
                $record = $this->$action($count);
                $data[] = $record;
            } catch (Exception $e) {
                echo "pass";
            } finally {
                $count++;
            }
        }
        $this->browser->close();
        return $data;
    }

    //Laptop
    function crawlLaptop($quantity): array
    {
        return $this->crawl("getLaptop", $quantity);
    }

    function getLaptopSelector(int $count): array
    {
        return [
            'imgSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__img > a img",
            'linkSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__img > a",
            'nameSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > h3 > a",
            'screenSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(1)",
            'cpuSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(2)",
            'ramSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(3)",
            'diskSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(4)",
            'cardSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(5)",
            'weightSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(6)",

        ];
    }

    function mappingLaptop(int $count): array
    {
        $selectors = $this->getLaptopSelector($count);

        $imgElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['imgSelector']));
        $linkElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['linkSelector']));
        $nameElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['nameSelector']));
        $screenElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['screenSelector']));
        $cpuElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['cpuSelector']));
        $ramElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['ramSelector']));
        $diskElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['diskSelector']));
        $cardElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['cardSelector']));
        $weightElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['weightSelector']));


        return [
            'imgElement' => $imgElement,
            'linkElement' => $linkElement,
            'nameElement' => $nameElement,
            'cpuElement' => $cpuElement,
            'screenElement' => $screenElement,
            'ramElement' => $ramElement,
            'diskElement' => $diskElement,
            'cardElement' => $cardElement,
            'weightElement' => $weightElement,
        ];
    }

    function getLaptop(int $count): array
    {
        $elements = $this->mappingLaptop($count);

        $name = $elements['nameElement']->getText();
        $img = $elements['imgElement']->getAttribute('src');
        $link = $elements['linkElement']->getAttribute('href');
        $cpu = $elements['cpuElement']->getText();
        $screen = $elements['screenElement']->getText();
        $ram = $elements['ramElement']->getText();
        $disk = $elements['diskElement']->getText();
        $card = $elements['cardElement']->getText();
        $weight = $elements['weightElement']->getText();

        return [
            'name' => $name,
            'img' => $img,
            'link' => $link,
            'cpu' => $cpu,
            'screen' => $screen,
            'ram' => $ram,
            'disk' => $disk,
            'card' => $card,
            'weight' => $weight,
        ];
    }


    //Phone
    function crawlPhone($quantity): array 
    {
        return $this->crawl("getPhone", $quantity);
    }

    function getPhoneSelector(int $count): array
    {
        return [
            'imgSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__img > a img",
            'linkSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__img > a",
            'nameSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > h3 > a",
            'cpuSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(1)",
            'sizeSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(2)",
            'ramSelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(3)",
            'memorySelector' => "#root > main > div > div.row.fspdbox > div.col-9.p-0 > div.card.fplistbox > div > div.cdt-product-wrapper.m-b-20 > div:nth-child($count) > div.cdt-product__info > div:nth-last-child(2) > div.cdt-product__config__param > span:nth-child(4)",
        ];
    }

    function mappingPhone(int $count): array
    {
        $selectors = $this->getPhoneSelector($count);

        $imgElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['imgSelector']));
        $linkElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['linkSelector']));
        $nameElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['nameSelector']));
        $cpuElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['cpuSelector']));
        $sizeElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['sizeSelector']));
        $ramElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['ramSelector']));
        $memoryElement = $this->browser->findElement(WebDriverBy::cssSelector($selectors['memorySelector']));

        return [
            'imgElement' => $imgElement,
            'linkElement' => $linkElement,
            'nameElement' => $nameElement,
            'cpuElement' => $cpuElement,
            'sizeElement' => $sizeElement,
            'ramElement' => $ramElement,
            'memoryElement' => $memoryElement,
        ];
    }

    function getPhone(int $count): array
    {
        $elements = $this->mappingPhone($count);

        $name = $elements['nameElement']->getText();
        $img = $elements['imgElement']->getAttribute('src');
        $link = $elements['linkElement']->getAttribute('href');
        $cpu = $elements['cpuElement']->getText();
        $size = $elements['sizeElement']->getText();
        $ram = $elements['ramElement']->getText();
        $memory = $elements['memoryElement']->getText();

        return [
            'name' => $name,
            'img' => $img,
            'link' => $link,
            'cpu' => $cpu,
            'size' => $size,
            'ram' => $ram,
            'memory' => $memory,
        ];
    }

    function crawlDataToJsonFile($filename, $data): void  {
        $jsonFile = fopen($filename, "w");
        $jsonData = json_encode($data);
        fwrite($jsonFile, $jsonData);
        fclose($jsonFile);
    }
}

