<?php
error_reporting(E_ALL);

require __DIR__.'/classes/TrueMarketSentiment.php';
require __DIR__.'/classes/VolumeTradeReview.php';

$volumeTradeReviewObj = new VolumeTradeReview('volume-trade-distribution.txt');
$dataArr = $volumeTradeReviewObj->getData();

$tmsObj = new TrueMarketSentiment('true-market-sentiment.txt');
$dataArrTMS = $tmsObj->getChartDataForTrueMarketSentiment();
$dataTMSStatus = $tmsObj->getTrueMarketSentimentStatus();

$dataPointsVol = $volumeTradeReviewObj->getChartDataForVolumeDistribution();
$dataPointsTrade = $volumeTradeReviewObj->getChartDataForTradeDistribution();
$dataArrBuySellMid = $volumeTradeReviewObj->getChartDataForBuyUpSellDownMidPriceDistribution();
$dataBuyUpArr = $dataArrBuySellMid['buyup'];
$dataSellDownArr = $dataArrBuySellMid['selldown'];
$dataMidPriceArr = $dataArrBuySellMid['midprice'];
$dataTMSTotalValue = $dataArrTMS['total_value'];
$dataTMSNetValue = $dataArrTMS['net_value'];

?>

<!DOCTYPE HTML>
<html>
<head>
    <style>
        #div2 {
            padding-top: 20px;
        }
    </style>
    <script>
        window.onload = function() {

            var chartVol = new CanvasJS.Chart("chartContainerVol", {
                backgroundColor: "#0A2466",
                animationEnabled: true,
                exportEnabled: true,
                title:{
                    text: "Volume Distribution",
                    fontSize: 16,
                    fontColor: "white"
                },
                axisX:{
                    interval: 1,
                    labelFontSize: 12,
                    labelFontColor: "white"
                },
                axisY: {
                    suffix:  "%",
                    labelFontSize: 12,
                    labelFontColor: "white",
                    minimum: 0
                },
                data: [{
                    type: "bar",
                    color: "#FEB20B",
                    indexLabelFontSize: 12,
                    yValueFormatString: "##.#0 '%'",
                    indexLabel: "{y}",
                    indexLabelPlacement: "auto",
                    indexLabelFontWeight: "bolder",
                    indexLabelFontColor: "white",
                    dataPoints: <?php echo json_encode($dataPointsVol, JSON_NUMERIC_CHECK); ?>
                }]
            });

            var chartTrade = new CanvasJS.Chart("chartContainerTrade", {
                backgroundColor: "#0A2466",
                animationEnabled: true,
                exportEnabled: true,
                title:{
                    text: "Trade Distribution",
                    fontSize: 16,
                    fontColor: "white"
                },
                axisX:{
                    interval: 1,
                    labelFontSize: 12,
                    labelFontColor: "white"
                },
                axisY: {
                    suffix:  "%",
                    labelFontSize: 12,
                    labelFontColor: "white",
                    minimum: 0
                },
                data: [{
                    type: "bar",
                    color: "#FEB20B",
                    indexLabelFontSize: 12,
                    yValueFormatString: "##.#0 '%'",
                    indexLabel: "{y}",
                    indexLabelPlacement: "auto",
                    indexLabelFontWeight: "bolder",
                    indexLabelFontColor: "white",
                    dataPoints: <?php echo json_encode($dataPointsTrade, JSON_NUMERIC_CHECK); ?>
                }]
            });

            var chartBuyMidSell = new CanvasJS.Chart("chartContainerBuyMidSell", {
                backgroundColor: "#0A2466",
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Buy Up, Mid Price and Sell Distribution (Volume)",
                    fontSize: 16,
                    fontColor: "white"
                },
                axisX:{
                    reversed: true,
                    interval: 1,
                    labelFontSize: 12,
                    labelFontColor: "white"
                },
                axisY: {
                    labelFontSize: 12,
                    labelFontColor: "white",
                    minimum: 0
                },
                toolTip:{
                    shared: true
                },
                data: [{
                    type: "stackedBar",
                    name: "Buy Up",
                    color: "#66A360",
                    labelFontSize: 12,
                    dataPoints: <?php echo json_encode($dataBuyUpArr, JSON_NUMERIC_CHECK); ?>
                },{
                    type: "stackedBar",
                    name: "Mid Price",
                    color: "white",
                    labelFontSize: 12,
                    dataPoints: <?php echo json_encode($dataMidPriceArr, JSON_NUMERIC_CHECK); ?>
                },{
                    type: "stackedBar",
                    name: "Sell Down",
                    color: "#DD4A46",
                    labelFontSize: 12,
                    indexLabel: "#total",
                    indexLabelPlacement: "outside",
                    indexLabelFontSize: 12,
                    indexLabelFontWeight: "bold",
                    indexLabelFontColor: "white",
                    dataPoints: <?php echo json_encode($dataSellDownArr, JSON_NUMERIC_CHECK); ?>
                }]
            });

            var chartTMS = new CanvasJS.Chart("chartContainerTMS", {
                backgroundColor: "#0A2466",
                exportEnabled: true,
                title: {
                    text: "True Market Sentiment - <?php echo $dataTMSStatus; ?>",
                    fontSize: 16,
                    fontColor: "white",
                    margin: 30
                },
                animationEnabled: true,
                toolTip:{
                    shared: true,
                    reversed: true
                },
                axisX:{
                    interval: 1,
                    labelFontSize: 12,
                    labelFontColor: "white"
                },
                axisY: {
                    title: "",
                    tickLength: 0,
                    lineThickness:0,
                    margin:0,
                    labelFormatter: function(e) {
                        return "";
                    }
                },
                legend: {
                    fontColor: "white"
                },
                data: [
                    {
                        type: "stackedColumn100",
                        name: "Total Value",
                        color: "#FFFFFF",
                        showInLegend: false,
                        indexLabel: "{y}",
                        indexLabelFontSize: 12,
                        indexLabelPlacement: "inside",
                        indexLabelFontWeight: "bolder",
                        indexLabelFontColor: "black",
                        dataPoints: <?php echo json_encode($dataTMSTotalValue, JSON_NUMERIC_CHECK); ?>
                    },{
                        type: "stackedColumn100",
                        name: "Net Value",
                        showInLegend: false,
                        indexLabel: "{y}",
                        indexLabelFontSize: 12,
                        indexLabelPlacement: "inside",
                        indexLabelFontWeight: "bolder",
                        indexLabelFontColor: "black",
                        dataPoints: <?php echo json_encode($dataTMSNetValue, JSON_NUMERIC_CHECK); ?>
                    }
                ]
            });

            chartVol.render();
            chartTrade.render();
            chartTMS.render();

        }
    </script>
</head>
<body style="background: white;">
<div id="container">
    <div id="div1">
        <div id="chartContainerTrade" style="height: 800px; width: 49.5%; float: left;"></div>
        <div id="chartContainerVol" style="height: 800px; width: 49.5%; float: right;"></div>
    </div>

    <div id="div2" style="clear: both;">
        <div id="chartContainerTMS" style="height: 300px; width: 100%;"></div>
    </div>
</div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>
