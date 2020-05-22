<?php
error_reporting(E_ALL);

require __DIR__.'/classes/TrueMarketSentiment.php';
require __DIR__.'/classes/VolumeTradeReview.php';

$volumeTradeReviewObj = new VolumeTradeReview('volume-trade-distribution.txt');
$dataArr = $volumeTradeReviewObj->getData();

$tmsObj = new TrueMarketSentiment('true-market-sentiment.txt');
$dataArrTMS = $tmsObj->getChartDataForTrueMarketSentiment();
$dataTMSStatus = $tmsObj->getTrueMarketSentimentStatus();
$dataTMSBrokersStats = $tmsObj->getTrueMarketSentiment();

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
                backgroundColor: "#042439",
                animationEnabled: true,
                exportEnabled: true,
                zoomEnabled: true,
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
                    minimum: 0,
                    gridThickness: 0
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
                backgroundColor: "#042439",
                animationEnabled: true,
                exportEnabled: true,
                zoomEnabled: true,
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
                    minimum: 0,
                    gridThickness: 0
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
                backgroundColor: "#042439",
                animationEnabled: true,
                exportEnabled: true,
                zoomEnabled: true,
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
                backgroundColor: "#042439",
                dataPointWidth: 100,
                exportEnabled: true,
                zoomEnabled: true,
                title: {
                    text: "Market Sentiment - <?php echo $dataTMSStatus['status']; ?>",
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
                    labelAutoFit: true,
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
                    },
                    gridThickness: 0,
                    tickLength: 0,
                    lineThickness: 0
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
<body style="background:#DCD8BB;">
<div id="container">
    <div id="div1">
        <div id="chartContainerTrade" style="height: 500px; width: 49.6%; float: left;"></div>
        <div id="chartContainerVol" style="height: 500px; width: 49.6%; float: right;"></div>
    </div>

    <div id="div2" style="clear: both;">
        <div id="chartContainerTMS" style="height: 350px; width: 100%;"></div>
    </div>
    <table style="width:100%; margin-top:20px;" border=1px cellspacing=0px cellpadding=0px>
    <tr>
        <th>&nbsp;</th>
        <?php
            foreach($dataTMSBrokersStats as $broker => $stats) 
            {
                echo '<th>'.$broker.'</th>';
            }
        ?>
    </tr>

    <tr>
        <td colspan=11>&nbsp;</td>
    </tr>

    <tr>
        <td>Buying Average</td>
        <?php
            // buying average
            foreach($dataTMSBrokersStats as $broker => $stats) 
            {
                echo '<td>'.$stats['buying_average'].'</td>';
            }
        ?>
    </tr>

    <tr>
        <td>Selling Average</td>
        <?php
            // selling average
            foreach($dataTMSBrokersStats as $broker => $stats) 
            {
                echo '<td>'.$stats['selling_average'].'</td>';
            }
        ?>
    </tr>

    <tr>
        <td colspan=11>&nbsp;</td>
    </tr>

    <tr>
        <td>Total Value</td>
        <?php
            // total value
            foreach($dataTMSBrokersStats as $broker => $stats) 
            {
                echo '<td>'.number_format($stats['total_value']).'</td>';
            }
        ?>
    </tr>

    <tr>
        <td>Net Value</td>
        <?php
            // net value
            foreach($dataTMSBrokersStats as $broker => $stats) 
            {
                echo '<td>'.number_format($stats['net_value']).'</td>';
            }
        ?>
    </tr>

    </table>

    <?php
        echo "<pre>";
        print_r($dataTMSStatus);
        echo "</pre>";
    ?>
</div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>
