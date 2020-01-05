<?php

final class TrueMarketSentiment
{
    CONST AVERAGE = 3;

    CONST BUYERS = 0;

    CONST VALUE = 2;

    CONST VOLUME = 1;

    CONST WEIGHT = 4;

    /**
     * @var $dataArr
     */
    private $dataArr;

    /**
     * @var $numBrokersGreenBar
     */
    private $numBrokersGreenBar = 0;

    /**
     * @var $numHigherBuyAvgThanSellAvg
     */
    private $numHigherBuyAvgThanSellAvg = 0;

    /**
     * @var $numLessBuyAvgThanSellAvg
     */
    private $numLessBuyAvgThanSellAvg = 0;

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
     * Get all the brokers with the total value (buyer + seller value).
     * Sorted in total value descending order.
     *
     * @return mixed[]
     */
    public function getAllBrokersWithTotalValue()
    {
        $data = [];
        $brokers = $this->getAllBrokers();
        foreach ($brokers as $broker) {
            $data[$broker] = $this->getTotalValuePerBroker($broker);
        }
        arsort($data);

        return $data;
    }

    /**
     * Get the chart data for the true market sentiment.
     *
     * @return mixed[]
     */
    public function getChartDataForTrueMarketSentiment()
    {
        $counter = 0;
        $data = [];
        $top10BrokersTMS = array_slice($this->getTrueMarketSentiment(), 0, 10);
        foreach ($top10BrokersTMS as $broker => $arrData) {
            $data['net_value'][$counter] = [
                'color' => ($arrData['net_value'] > 0) ? '#66A360' : '#DD4A47',
                'label' => $broker,
                'y' => $arrData['net_value']
            ];

            $data['total_value'][$counter] = [
                'label' => $broker,
                'y' => $arrData['total_value']
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
    public function getData()
    {
        return $this->dataArr;
    }

    /**
     * Get the stats.
     *
     * @return mixed[]
     */
    public function getStats()
    {
        $data = [];
        $numBrokersGreenBar = 0;
        $numHigherBuyAvgThanSellAvg = 0;
        $numLessBuyAvgThanSellAvg = 0;
        $brokersSentiment = array_slice($this->getTrueMarketSentiment(), 0, 10);
        foreach ($brokersSentiment as $broker => $brokerData) {
            if ($brokerData['net_value'] > 0) {
                $numBrokersGreenBar++;
            }

            if ($brokerData['buying_average'] > $brokerData['selling_average']) {
                $numHigherBuyAvgThanSellAvg++;
            } else {
                $numLessBuyAvgThanSellAvg++;
            }
        }

        $this->numBrokersGreenBar = $numBrokersGreenBar;
        $this->numHigherBuyAvgThanSellAvg = $numHigherBuyAvgThanSellAvg;
        $this->numLessBuyAvgThanSellAvg = $numLessBuyAvgThanSellAvg;

        $data = [
            'numBrokersGreenBar' => $this->numBrokersGreenBar,
            'numHigherBuyAvgThanSellAvg' => $this->numHigherBuyAvgThanSellAvg,
            'numLessBuyAvgThanSellAvg' => $this->numLessBuyAvgThanSellAvg
        ];

        return $data;
    }

    /**
     * Get the total value by status.
     *
     * @param $status
     *
     * @return int
     */
    public function getTotalValueByStatus($status)
    {
        return $this->getSumFromAssocArray($status, self::VALUE);
    }

    /**
     * Get the total volume by status.
     *
     * @param $status
     *
     * @return int
     */
    public function getTotalVolumeByStatus($status)
    {
        return $this->getSumFromAssocArray($status, self::VOLUME);
    }

    /**
     * Get the true market sentiment.
     *
     * @return mixed[]
     */
    public function getTrueMarketSentiment()
    {
        $data = [];
        $brokers = array_slice($this->getAllBrokersWithTotalValue(), 0, 10);
        foreach ($brokers as $broker => $totalValue) {
            $buyingAverage = 0;
            $buyingValue = 0;
            if (array_key_exists($broker, $this->dataArr['buyers'])) {
                $buyingAverage = $this->dataArr['buyers'][$broker][self::AVERAGE];
                $buyingValue = $this->convertIntWithCommatoIntWithNoComma(
                    $this->dataArr['buyers'][$broker][self::VALUE]
                );
            }

            $sellingAverage = 0;
            $sellingValue = 0;
            if (array_key_exists($broker, $this->dataArr['sellers'])) {
                $sellingAverage = $this->dataArr['sellers'][$broker][self::AVERAGE];
                $sellingValue = $this->convertIntWithCommatoIntWithNoComma(
                    $this->dataArr['sellers'][$broker][self::VALUE]
                );
            }

            $netValue = $buyingValue - $sellingValue;

            $data[$broker] = [
                'buying_average' => $buyingAverage,
                'buying_value' => $buyingValue,
                'net_value' => $netValue,
                'selling_average' => $sellingAverage,
                'selling_value' => $sellingValue,
                'total_value' => $totalValue,
                'stats' => [
                    'is_green_candle' => ($netValue > 0) ? 'true' : 'false',
                    'is_higher_buy_avg_than_sell_avg' => ($buyingAverage > $sellingAverage) ? 'true' : 'false',
                    'is_less_buy_avg_than_sell_avg' => ($buyingAverage < $sellingAverage) ? 'true' : 'false'
                ]
            ];
        }

        return $data;
    }

    /**
     * Get the true market sentiment status.
     *
     * @return string
     */
    public function getTrueMarketSentimentStatus()
    {
        $status = 'NEUTRAL';
        $stats = $this->getStats();

        if ($stats['numBrokersGreenBar'] > 5 && $stats['numHigherBuyAvgThanSellAvg'] > 5) {
            $status = 'BULLISH';
        } else {
            if (
                ($stats['numBrokersGreenBar'] === 5 && $stats['numHigherBuyAvgThanSellAvg'] === 5) ||
                ($stats['numBrokersGreenBar'] > 5 && $stats['numHigherBuyAvgThanSellAvg'] < 5) ||
                ($stats['numBrokersGreenBar'] < 5 && $stats['numHigherBuyAvgThanSellAvg'] > 5)
            ) {
                $status = 'NEUTRAL';
            } else {
                if ($stats['numBrokersGreenBar'] < 5 && $stats['numHigherBuyAvgThanSellAvg'] < 5) {
                    $status = 'BEARISH';
                }
            }
        }

        return $status;
    }

    /**
     * Set the data.
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
    private function convertIntWithCommatoIntWithNoComma($value)
    {
        return (int)str_replace(',', '', $value);
    }

    /**
     * Get's all the brokers from both buyers and sellers.
     *
     * @return mixed[]
     */
    public function getAllBrokers()
    {
        $brokers = array_keys(
            array_merge(
                $this->dataArr['buyers'],
                $this->dataArr['sellers']
            )
        );

        return $brokers;
    }

    /**
     * Get data from text file.
     *
     * @param $filename
     *
     * @return mixed[]
     */
    private function getDataFromFile($filename)
    {
        $filePointer = fopen($filename, 'r');
        $status = 'buyers';
        while (!feof($filePointer)) {
            $line = fgets($filePointer, 2048);

            $rawDataArr = str_getcsv($line, "\t");

            if ($rawDataArr[0] == '') {
                $status = 'sellers';
            } else {
                $broker = $rawDataArr[0];
                $this->dataArr[$status][$broker] = array_map('trim', $rawDataArr);
            }
        }

        fclose($filePointer);

        return $this->dataArr;
    }

    /**
     * Helper function to add the values in an associative array.
     *
     * @param $status
     * @param $key
     *
     * @return int
     */
    private function getSumFromAssocArray($status, $key)
    {
        $total = 0;
        $data = $this->getData();
        foreach ($data[$status] as $keyData) {
            $total += $this->convertIntWithCommatoIntWithNoComma($keyData[$key]);
        }

        return $total;
    }

    /**
     * Get's all the brokers from both buyers and sellers.
     *
     * @param $broker
     *
     * @return float
     */
    private function getTotalValuePerBroker($broker)
    {
        $buyerValue = 0;
        $sellerValue = 0;
        if (array_key_exists($broker, $this->dataArr['buyers'])) {
            $buyerValue = $this->convertIntWithCommatoIntWithNoComma(
                $this->dataArr['buyers'][$broker][self::VALUE]
            );
        }

        if (array_key_exists($broker, $this->dataArr['sellers'])) {
            $sellerValue = $this->convertIntWithCommatoIntWithNoComma(
                $this->dataArr['sellers'][$broker][self::VALUE]
            );
        }

        return ($buyerValue + $sellerValue);
    }
}
