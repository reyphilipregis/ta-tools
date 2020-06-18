<?php

final class VolumeTradeReview
{
    CONST BUYUP = 3;

    CONST MIDPRICE = 4;

    CONST PRICE = 0;

    CONST SELLDOWN = 5;

    CONST TRADES = 6;

    CONST VOLUMETRADED = 1;

    /**
     * @var $dataArr
     */
    private $dataArr;

    /**
     * VolumeTradeReview constructor.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->setData($this->getDataFromFile($filename));
    }

    /**
     * Get the chart data format for buy up, sell down and mid price distribution.
     *
     * @return mixed[]
     */
    public function getChartDataForBuyUpSellDownMidPriceDistribution(): array
    {
        $data = [];
        $counter = 0;
        $rawData = array_reverse($this->getData());
        foreach ($rawData as $dataPerPrice) {
            $data['buyup'][$counter] = [
                'label' => (string)$dataPerPrice[self::PRICE],
                'y' => ($dataPerPrice[self::BUYUP] === '') ?
                    0 : $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::BUYUP])
            ];

            $data['selldown'][$counter] = [
                'label' => (string)$dataPerPrice[self::PRICE],
                'y' => ($dataPerPrice[self::SELLDOWN] === '') ?
                    0 : $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::SELLDOWN])
            ];

            $data['midprice'][$counter] = [
                'label' => (string)$dataPerPrice[self::PRICE],
                'y' => ($dataPerPrice[self::MIDPRICE] === '') ?
                    0 : $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::MIDPRICE])
            ];

            $counter++;
        }

        return $data;
    }

    /**
     * Get the chart data format for trade distribution.
     *
     * @return mixed[]
     */
    public function getChartDataForTradeDistribution(): array
    {
        $data = [];
        $totalNumberOfTrades = $this->getTotalTNumberOfTrades();
        $counter = 0;
        foreach ($this->getData() as $dataPerPrice) {
            $numTradesPerPrice = $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::TRADES]);
            $percentage = round(($numTradesPerPrice / $totalNumberOfTrades) * 100, 2);

            $data[$counter] = [
                'label' => (string)$dataPerPrice[self::PRICE],
                'y' => $percentage
            ];

            $counter++;
        }

        return $data;
    }

    /**
     * Get the chart data format for volume distribution.
     *
     * @return mixed[]
     */
    public function getChartDataForVolumeDistribution(): array
    {
        $data = [];
        $totalVolume = $this->getTotalVolumeTraded();
        $counter = 0;
        foreach ($this->getData() as $dataPerPrice) {
            $volumeTradedPerPrice = $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::VOLUMETRADED]);
            $percentage = round(($volumeTradedPerPrice / $totalVolume) * 100, 2);
            $data[$counter] = [
                'label' => (string)$dataPerPrice[self::PRICE],
                'y' => $percentage
            ];

            $counter++;
        }

        return $data;
    }

    /**
     * Get the data.
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->dataArr;
    }

    /**
     * Get total buy up.
     *
     * @return int
     */
    public function getTotalBuyUp(): int
    {
        return $this->getSumFromAssocArray(self::BUYUP);
    }

    /**
     * Get total mid price.
     *
     * @return int
     */
    public function getTotalMidPrice()
    {
        return $this->getSumFromAssocArray(self::MIDPRICE);
    }

    /**
     * Get total sell down.
     *
     * @return int
     */
    public function getTotalSellDown(): int
    {
        return $this->getSumFromAssocArray(self::SELLDOWN);
    }

    /**
     * Get the total number of trades.
     *
     * @return int
     */
    public function getTotalTNumberOfTrades(): int
    {
        return $this->getSumFromAssocArray(self::TRADES);
    }

    /**
     * Get total volume traded.
     *
     * @return int
     */
    public function getTotalVolumeTraded(): int
    {
        return $this->getSumFromAssocArray(self::VOLUMETRADED);
    }

    /**
     * Get trade distribution per price.
     *
     * @return mixed[]
     */
    public function getTradeDistribution(): array
    {
        $data = [];
        $totalNumberOfTrades = $this->getTotalTNumberOfTrades();
        foreach ($this->getData() as $dataPerPrice) {
            $numTradesPerPrice = $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::TRADES]);
            $percentage = round(($numTradesPerPrice / $totalNumberOfTrades) * 100, 2);
            $data[$dataPerPrice[self::PRICE]] = $percentage;
        }

        return $data;
    }

    /**
     * Get the volume and trade distribution per price.
     *
     * @return mixed[]
     */
    public function getVolumeAndTradeDistribution(): array
    {
        $data = [];
        $totalVolume = $this->getTotalVolumeTraded();
        $totalNumberOfTrades = $this->getTotalTNumberOfTrades();
        foreach ($this->getData() as $dataPerPrice) {
            $volumeTradedPerPrice = $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::VOLUMETRADED]);
            $numTradesPerPrice = $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::TRADES]);
            $percentageVolume = round(($volumeTradedPerPrice / $totalVolume) * 100, 2);
            $percentageNumTrades = round(($numTradesPerPrice / $totalNumberOfTrades) * 100, 2);

            $data[$dataPerPrice[self::PRICE]] = [
                'volume_percentage' => $percentageVolume,
                'trades_percentage' => $percentageNumTrades
            ];
        }

        return $data;
    }

    /**
     * Get volume distribution per price.
     *
     * @return mixed[]
     */
    public function getVolumeDistribution(): array
    {
        $data = [];
        $totalVolume = $this->getTotalVolumeTraded();
        foreach ($this->getData() as $dataPerPrice) {
            $volumeTradedPerPrice = $this->convertIntWithCommatoIntWithNoComma($dataPerPrice[self::VOLUMETRADED]);
            $percentage = round(($volumeTradedPerPrice / $totalVolume) * 100, 2);
            $data[$dataPerPrice[self::PRICE]] = $percentage;
        }

        return $data;
    }

    /**
     * Set the data
     *
     * @param $data
     *
     * @return void
     */
    public function setData($data)
    {
        $this->dataArr = $data;
    }

    /**
     * Helper function that will convert an int with comma to an int without comma.
     *
     * @param $value
     *
     * @return int
     */
    private function convertIntWithCommatoIntWithNoComma($value): int
    {
        return (int)str_replace(',', '', $value);
    }

    /**
     * Get data from text file.
     *
     * @param $filename
     *
     * @return mixed[]
     */
    private function getDataFromFile($filename): array
    {
        $filePointer = fopen($filename, 'rb');
        while (!feof($filePointer)) {
            $line = fgets($filePointer, 2048);

            $rawDataArr = str_getcsv($line, "\t");
            $this->dataArr[$rawDataArr[self::PRICE]] = array_map('trim', $rawDataArr);
        }

        fclose($filePointer);

        return $this->dataArr;
    }

    /**
     * Helper function to add the values in an associative array.
     *
     * @param $key
     *
     * @return int
     */
    private function getSumFromAssocArray($key): int
    {
        $total = 0;
        foreach ($this->getData() as $keyData) {
            $total += $this->convertIntWithCommatoIntWithNoComma($keyData[$key]);
        }

        return $total;
    }
}
