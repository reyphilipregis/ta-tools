<?php

require 'Broker.php';

final class TrueMarketSentiment
{
    CONST AVERAGE = 3;

    CONST BUYERS = 0;

    CONST MAX_NUM_BROKERS = 10;

    CONST STATUS_BEARISH = 'BEARISH';

    CONST STATUS_BULLISH = 'BULLISH';

    CONST STATUS_NEUTRAL = 'NEUTRAL';

    CONST STAT_FALSE = 'false';

    CONST STAT_TRUE = 'true';

    CONST VALUE = 2;

    CONST VOLUME = 1;

    CONST WEIGHT = 4;

    /**
     * @var $broker
     */
    private $broker;

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
        $this->broker = new Broker();
        $this->setData($this->getDataFromFile($filename));
    }

    /**
     * Get's all the brokers from both buyers and sellers.
     *
     * @return mixed[]
     */
    public function getAllBrokers(): array
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
     * Get all the brokers with the total value (buyer + seller value).
     * Sorted in total value descending order.
     *
     * @return mixed[]
     */
    public function getAllBrokersWithTotalValue(): array
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
    public function getChartDataForTrueMarketSentiment(): array
    {
        $counter = 0;
        $data = [];
        $top10BrokersTMS = array_slice($this->getTrueMarketSentiment(), 0, self::MAX_NUM_BROKERS);
        foreach ($top10BrokersTMS as $broker => $arrData) {
            $data['net_value'][$counter] = [
                'color' => ($arrData['net_value'] > 0) ? '#146600' : '#B70016',
                'label' => $this->broker->getBroker($broker),
                'y' => $arrData['net_value']
            ];

            $data['total_value'][$counter] = [
                'label' => $this->broker->getBroker($broker),
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
    public function getData(): array
    {
        return $this->dataArr;
    }

    /**
     * Get the stats.
     *
     * @return mixed[]
     */
    public function getStats(): array
    {
        $numBrokersGreenBar = 0;
        $nmHighBuyAvgThnSell = 0;
        $nmLessBuyAvgThnSell = 0;
        $brokersSentiment = array_slice($this->getTrueMarketSentiment(), 0, self::MAX_NUM_BROKERS);
        foreach ($brokersSentiment as $broker => $brokerData) {
            if ($brokerData['net_value'] > 0) {
                $numBrokersGreenBar++;
            }

            if ($brokerData['buying_average'] > $brokerData['selling_average']) {
                $nmHighBuyAvgThnSell++;
            } else {
                $nmLessBuyAvgThnSell++;
            }
        }

        $this->numBrokersGreenBar = $numBrokersGreenBar;
        $this->numHigherBuyAvgThanSellAvg = $nmHighBuyAvgThnSell;
        $this->numLessBuyAvgThanSellAvg = $nmLessBuyAvgThnSell;

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
    public function getTotalValueByStatus($status): int
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
    public function getTotalVolumeByStatus($status): int
    {
        return $this->getSumFromAssocArray($status, self::VOLUME);
    }

    /**
     * Get the true market sentiment.
     *
     * @return mixed[]
     */
    public function getTrueMarketSentiment(): array
    {
        $data = [];
        $brokers = array_slice($this->getAllBrokersWithTotalValue(), 0, self::MAX_NUM_BROKERS);
        foreach ($brokers as $broker => $totalValue) {
            $buyingAverage = 0;
            $buyingValue = 0;
            if (array_key_exists($broker, $this->dataArr['buyers'])) {
                $buyingAverage = $this->dataArr['buyers'][$broker][self::AVERAGE];
                $buyingValue = $this->convertIntWithCommaToIntWithNoComma(
                    $this->dataArr['buyers'][$broker][self::VALUE]
                );
            }

            $sellingAverage = 0;
            $sellingValue = 0;
            if (array_key_exists($broker, $this->dataArr['sellers'])) {
                $sellingAverage = $this->dataArr['sellers'][$broker][self::AVERAGE];
                $sellingValue = $this->convertIntWithCommaToIntWithNoComma(
                    $this->dataArr['sellers'][$broker][self::VALUE]
                );
            }

            $netValue = $buyingValue - $sellingValue;

            $data[$broker] = [
                'buying_average' => $buyingAverage,
                'selling_average' => $sellingAverage,
                'buying_value' => $buyingValue,
                'selling_value' => $sellingValue,
                'net_value' => $netValue,
                'total_value' => $totalValue,
                'stats' => [
                    'is_green_candle' => ($netValue > 0) ? self::STAT_TRUE : self::STAT_FALSE,
                    'is_higher_buy_avg_than_sell_avg' => ($buyingAverage > $sellingAverage) ? self::STAT_TRUE : self::STAT_FALSE,
                    'is_less_buy_avg_than_sell_avg' => ($buyingAverage < $sellingAverage) ? self::STAT_TRUE : self::STAT_FALSE
                ]
            ];
        }

        return $data;
    }

    /**
     * Get the true market sentiment status.
     *
     * @return mixed[]
     */
    public function getTrueMarketSentimentStatus(): array
    {
        $status = self::STATUS_NEUTRAL;
        $stats = $this->getStats();

        if ($stats['numBrokersGreenBar'] > 5 && $stats['numHigherBuyAvgThanSellAvg'] > 5) {
            $status = self::STATUS_BULLISH;
        } elseif (
            ($stats['numBrokersGreenBar'] >= 5 && $stats['numHigherBuyAvgThanSellAvg'] >= 5) ||
            ($stats['numBrokersGreenBar'] > 5 && $stats['numHigherBuyAvgThanSellAvg'] < 5) ||
            ($stats['numBrokersGreenBar'] < 5 && $stats['numHigherBuyAvgThanSellAvg'] > 5)
        ) {
            $status = self::STATUS_NEUTRAL;
        } elseif ($stats['numBrokersGreenBar'] < 5 && $stats['numHigherBuyAvgThanSellAvg'] < 5) {
            $status = self::STATUS_BEARISH;
        }

        return [
            'status'                            => $status,
            'num_green_bar'                     => $stats['numBrokersGreenBar'],
            'num_higher_buy_avg_than_sell_avg'  => $stats['numHigherBuyAvgThanSellAvg']
        ];
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
    private function convertIntWithCommaToIntWithNoComma($value): int
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
    private function getSumFromAssocArray($status, $key): int
    {
        $total = 0;
        $data = $this->getData();
        foreach ($data[$status] as $keyData) {
            $total += $this->convertIntWithCommaToIntWithNoComma($keyData[$key]);
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
    private function getTotalValuePerBroker($broker): float
    {
        $buyerValue = 0;
        $sellerValue = 0;
        if (array_key_exists($broker, $this->dataArr['buyers'])) {
            $buyerValue = $this->convertIntWithCommaToIntWithNoComma(
                $this->dataArr['buyers'][$broker][self::VALUE]
            );
        }

        if (array_key_exists($broker, $this->dataArr['sellers'])) {
            $sellerValue = $this->convertIntWithCommaToIntWithNoComma(
                $this->dataArr['sellers'][$broker][self::VALUE]
            );
        }

        return ($buyerValue + $sellerValue);
    }
}
