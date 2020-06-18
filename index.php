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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script type="text/javascript">
            window.onload = function() {

                var chartTrade = new CanvasJS.Chart("chartContainerTrade", {
                    backgroundColor: "#F2F2F2",
                    animationEnabled: true,
                    exportEnabled: false,
                    zoomEnabled: true,
                    title:{
                        text: "Trade Distribution",
                        fontSize: 25,
                        fontColor: "#607D8B",
                        fontWeight: "normal",
                        margin: 5,
                        padding: {
                            top: 8
                        }
                    },
                    axisX:{
                        interval: 1,
                        labelFontSize: 12,
                        labelFontColor: "#091F57",
                    },
                    axisY: {
                        suffix:  "%",
                        labelFontSize: 12,
                        labelFontColor: "#091F57",
                        minimum: 0,
                        gridThickness: 0
                    },
                    data: [{
                        type: "bar",
                        color: "#607D8B",
                        indexLabelFontSize: 12,
                        yValueFormatString: "##.#0 '%'",
                        indexLabel: "{y}",
                        indexLabelPlacement: "auto",
                        indexLabelFontColor: "#091F57",
                        indexLabelBackgroundColor: "#ECEEB9",
                        indexLabelFontFamily: "tahoma",
                        dataPoints: <?php echo json_encode($dataPointsTrade, JSON_NUMERIC_CHECK); ?>
                    }]
                });

                var chartVol = new CanvasJS.Chart("chartContainerVol", {
                    backgroundColor: "#F2F2F2",
                    animationEnabled: true,
                    exportEnabled: false,
                    zoomEnabled: true,
                    title:{
                        text: "Volume Distribution",
                        fontSize: 25,
                        fontColor: "#607D8B",
                        fontWeight: "normal",
                        margin: 5,
                        padding: {
                            top: 8
                        }
                    },
                    axisX:{
                        interval: 1,
                        labelFontSize: 12,
                        labelFontColor: "#091F57"
                    },
                    axisY: {
                        suffix:  "%",
                        labelFontSize: 12,
                        labelFontColor: "#091F57",
                        minimum: 0,
                        gridThickness: 0
                    },
                    data: [{
                        type: "bar",
                        color: "#607D8B",
                        indexLabelFontSize: 12,
                        yValueFormatString: "##.#0 '%'",
                        indexLabel: "{y}",
                        indexLabelPlacement: "auto",
                        indexLabelFontColor: "#091F57",
                        indexLabelBackgroundColor: "#ECEEB9",
                        indexLabelFontFamily: "tahoma",
                        dataPoints: <?php echo json_encode($dataPointsVol, JSON_NUMERIC_CHECK); ?>
                    }]
                });

                var chartTMS = new CanvasJS.Chart("chartContainerTMS", {
                    backgroundColor: "#F2F2F2",
                    dataPointWidth: 90,
                    exportEnabled: false,
                    zoomEnabled: true,
                    title: {
                        text: "True Market Sentiment - <?php echo $dataTMSStatus['status']; ?>",
                        fontSize: 25,
                        fontColor: "#607D8B",
                        fontWeight: "normal",
                        margin: 50,
                        padding: {
                            top: 10
                        }
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
                        labelFontColor: "#091F57"
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
                    data: [
                        {
                            type: "stackedColumn100",
                            name: "Total Value",
                            color: "#c9c9c7",
                            showInLegend: false,
                            indexLabel: "{y}",
                            indexLabelFontSize: 12,
                            indexLabelPlacement: "inside",
                            indexLabelFontColor: "#092057",
                            indexLabelBackgroundColor: "#ECEEB9",
                            indexLabelFontFamily: "tahoma",
                            dataPoints: <?php echo json_encode($dataTMSTotalValue, JSON_NUMERIC_CHECK); ?>
                        },{
                            type: "stackedColumn100",
                            name: "Net Value",
                            showInLegend: false,
                            indexLabel: "{y}",
                            indexLabelFontSize: 12,
                            indexLabelPlacement: "inside",
                            indexLabelFontColor: "#092057",
                            indexLabelBackgroundColor: "#ECEEB9",
                            indexLabelFontFamily: "tahoma",
                            dataPoints: <?php echo json_encode($dataTMSNetValue, JSON_NUMERIC_CHECK); ?>
                        }
                    ]
                });

                chartVol.render();
                chartTrade.render();
                chartTMS.render();
            }
        </script>
        <title>Technical Analysis Tools</title>
    </head>
    <body>
        <div id="container">
            <div id="div1">
                <div id="chartContainerTrade"></div>
                <div id="chartContainerVol"></div>
            </div>

            <div id="div2">
                <div id="chartContainerTMS"></div>
            </div>
        </div>
        <script src="js/canvasjs.min.js"></script>
    </body>
</html>
