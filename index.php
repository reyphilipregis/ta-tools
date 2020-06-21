<?php
    error_reporting(E_ALL);

    require __DIR__.'/classes/TrueMarketSentiment.php';
    require __DIR__.'/classes/VolumeTradeReview.php';
    require __DIR__.'/config/config.php';
    
    $volumeTradeReviewObj = new VolumeTradeReview($config['TVD_FILE']);
    $dataArr = $volumeTradeReviewObj->getData();

    $tmsObj = new TrueMarketSentiment($config['TMS_FILE']);
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
                    backgroundColor: "#F7F7F7",
                    animationEnabled: true,
                    exportEnabled: false,
                    zoomEnabled: true,
                    title:{
                        text: "Trade Distribution",
                        fontSize: 35,
                        fontColor: "#29464B",
                        fontWeight: "normal",
                        margin: 5,
                        padding: {
                            top: 8
                        }
                    },
                    axisX:{
                        interval: 1,
                        labelFontSize: 20,
                        labelFontColor: "#091F57",
                    },
                    axisY: {
                        suffix:  "%",
                        labelFontSize: 20,
                        labelFontColor: "#091540",
                        minimum: 0,
                        gridThickness: 0
                    },
                    data: [{
                        type: "bar",
                        color: "#25A044",
                        indexLabelFontSize: 20,
                        yValueFormatString: "##.#0 '%'",
                        indexLabel: "{y}",
                        indexLabelPlacement: "auto",
                        indexLabelFontColor: "#000000",
                        indexLabelBackgroundColor: "#FEE45E",
                        indexLabelFontFamily: "tahoma",
                        dataPoints: <?php echo json_encode($dataPointsTrade, JSON_NUMERIC_CHECK); ?>
                    }]
                });

                var chartVol = new CanvasJS.Chart("chartContainerVol", {
                    backgroundColor: "#F7F7F7",
                    animationEnabled: true,
                    exportEnabled: false,
                    zoomEnabled: true,
                    title:{
                        text: "Volume Distribution",
                        fontSize: 35,
                        fontColor: "#29464B",
                        fontWeight: "normal",
                        margin: 5,
                        padding: {
                            top: 8
                        }
                    },
                    axisX:{
                        interval: 1,
                        labelFontSize: 20,
                        labelFontColor: "#091F57"
                    },
                    axisY: {
                        suffix:  "%",
                        labelFontSize: 20,
                        labelFontColor: "#091540",
                        minimum: 0,
                        gridThickness: 0
                    },
                    data: [{
                        type: "bar",
                        color: "#25A044",
                        indexLabelFontSize: 20,
                        yValueFormatString: "##.#0 '%'",
                        indexLabel: "{y}",
                        indexLabelPlacement: "auto",
                        indexLabelFontColor: "#000000",
                        indexLabelBackgroundColor: "#FEE45E",
                        indexLabelFontFamily: "tahoma",
                        dataPoints: <?php echo json_encode($dataPointsVol, JSON_NUMERIC_CHECK); ?>
                    }]
                });

                var chartTMS = new CanvasJS.Chart("chartContainerTMS", {
                    backgroundColor: "#F7F7F7",
                    dataPointWidth: 160,
                    exportEnabled: false,
                    zoomEnabled: true,
                    title: {
                        text: "True Market Sentiment - <?php echo $dataTMSStatus['status']; ?>",
                        fontSize: 35,
                        fontColor: "#29464B",
                        fontWeight: "normal",
                        margin: 50,
                        padding: {
                            top: 8
                        }
                    },
                    animationEnabled: true,
                    toolTip:{
                        shared: true,
                        reversed: true
                    },
                    axisX:{
                        labelMaxWidth: 150,
			            labelWrap: true,
                        labelAutoFit: true,
                        interval: 1,
                        labelFontSize: 22,
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
                            color: "#E2E2E2",
                            showInLegend: false,
                            indexLabel: "{y}",
                            indexLabelFontSize: 22,
                            indexLabelPlacement: "inside",
                            indexLabelFontColor: "#092057",
                            indexLabelBackgroundColor: "#FEE45E",
                            indexLabelFontFamily: "tahoma",
                            dataPoints: <?php echo json_encode($dataTMSTotalValue, JSON_NUMERIC_CHECK); ?>
                        },{
                            type: "stackedColumn100",
                            name: "Net Value",
                            showInLegend: false,
                            indexLabel: "{y}",
                            indexLabelFontSize: 22,
                            indexLabelPlacement: "inside",
                            indexLabelFontColor: "#092057",
                            indexLabelBackgroundColor: "#FEE45E",
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
