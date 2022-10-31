<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bản đồ bệnh viện trong TP Hà Nội</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>

    <link rel="stylesheet" href="http://localhost:8081/libs/openlayers/css/ol.css" type="text/css" />
    <script src="http://localhost:8081/libs/openlayers/build/ol.js" type="text/javascript"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>

    <script src="http://localhost:8081/libs/jquery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <style>
        /*
            .map, .righ-panel {
                height: 500px;
                width: 80%;
                float: left;
            }
            */
        * {
            padding: 0px;
            margin: 0px;
        }

        .body {
            padding: 0px;
            margin: 0px;
        }

        .container {
            padding: 0;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #daf5ee;
        }

        .map,
        .righ-panel {
            height: calc(100vh - 60px);
            width: 60vw;
            float: left;
            margin: 0px 8px;
        }

        .map {
            border: 1px solid #000;
            position: relative;
        }

        .search {
            display: flex;
            margin: 8px;

        }

        .search input {
            height: 30px;
            width: 200px;
            margin-right: 8px;
            padding: 0px 8px;
        }

        input:focus {
            border: blue;
        }

        .search .btnSearch {
            height: 35px;
            min-width: 70px;
            background-color: #fff;
            color: #000;
            border-radius: 4px;
        }

        .main {
            display: flex;

        }

        .header {
            padding: 10px 0px;
            text-align: center;
        }

        #info {
            position: absolute;
            z-index: 3;
            background-color: #fff;
            padding: 8px;
            font-size: 13px;
            top: 0px;
            right: 0px;
            border-radius: 8px;
            font-weight: 700 !important;

        }

        #info tr,
        #info table,
        #info td,
        #info th {
            border: none !important;
            font-weight: 700 !important;
        }

        table {
            font-weight: 100;
            border-spacing: 0px;
            border: 1px solid #bbb
        }

        tr {
            border: 1px solid #bbbb;
        }

        th {
            border-right: 1px solid #bbb
        }

        td {
            font-weight: 100;
            padding: 4px 8px;
            border-right: 1px solid #bbb;
            border-top: 1px solid #bbb
        }

        #btnReset,
        #xem,
        #all {
            height: 30px;
            min-width: 80px;
            background-color: #fff;
            color: #000;
            border-radius: 3px;
            margin: 8px 8px;
            font-weight: 700;

        }

        .searchResult {
            max-height: 300px;
            overflow-y: auto;
        }

        #kq_bv {
            max-height: 300px;
            overflow-y: auto;

        }

        #btnReset:hover,
        .btnSearch:hover,
        #xem:hover,
        #all:hover {
            background-color: #000;
            color: #fff;
        }
    </style>
</head>

<body onload="initialize_map();">
    <div class="container">
        <h1 class="header">Bản đồ bệnh viện trong TP Hà Nội</h1>

        <div class="main">
            <div id="map" class="map">


                <div id="info"></div>

            </div>
            <div>
                <h4>Nhập tên để tìm kiếm </h4>
                <div class="search">
                    <input type="text" id="search" style="height: 30px" placeholder="Search" />
                    <button class="btnSearch">Tìm kiếm</button>
                </div>
                <p id="error" style="color: red; margin-left: 8px; font-size:11px"></p>

                <div class="searchResult">

                </div>

                <button title="xem tất cả bệnh viện trên bản đồ" id="all">Xem tất cả</button><button id="btnReset">Làm mới</button>
                <br>

                <div id="kq"></div>

                <input type="checkbox" style="margin: 8px" id="title-search" value="1">Bệnh viện gần vị trí bạn chọn
                <div id="kq_bv"></div>
            
            </div>

        </div>

    </div>

    <?php include 'pgsqlAPI.php' ?>

    <script>
        //$("#document").ready(function () {
        var format = 'image/png';
        var map;
        var minX = 104.671219482422;
        var minY = 18.6605095458984;
        var maxX = 107.023750305176;
        var maxY = 23.38938331604;
        var cenX = (minX + maxX) / 2;
        var cenY = (minY + maxY) / 2;
        var mapLat = cenY;
        var mapLng = cenX;
        var mapDefaultZoom = 15;


        function initialize_map() {
            //*
            layerBG = new ol.layer.Tile({
                source: new ol.source.OSM({})
            });
            //*/
            var benhvienPolygon = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/benhvien/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.0',
                        STYLES: '',
                        LAYERS: 'benhvien',
                    }
                })
            });
            var viewMap = new ol.View({
                center: ol.proj.fromLonLat([mapLng, mapLat]),
                zoom: mapDefaultZoom
                //projection: projection
            });

            map = new ol.Map({
                target: "map",
                layers: [layerBG],
                //layers: [benhvienPolygon],
                view: viewMap
            });
            var isShow = false
            $("#all").click(function() {
                isShow = !isShow
                if (isShow == true) {
                    map.addLayer(benhvienPolygon)
                    $("#all").html("Ẩn tất cả")
                } else {
                    map.removeLayer(benhvienPolygon)
                    $("#all").html("Xem tất cả")
                    location.reload()

                }


            })


            var styles = {
                'MultiPolygon': new ol.style.Style({

                    fill: new ol.style.Fill({
                        color: 'orange',

                    }),
                    stroke: new ol.style.Stroke({
                        color: 'yellow',
                        width: 2
                    }),
                    text: new ol.style.Text({ //Text style
                        font: '12px Calibri,sans-serif',
                        fill: new ol.style.Fill({
                            color: '#000'
                        }),
                        stroke: new ol.style.Stroke({
                            color: '#fff',
                            width: 3
                        })
                    })


                })
            };
            var styleFunction = function(feature) {
                return styles[feature.getGeometry().getType()];
            };


            var vectorLayer = new ol.layer.Vector({
                //source: vectorSource,
                style: styleFunction
            });
          
            map.addLayer(vectorLayer);
         
            $("#btnReset").click(function() {
                location.reload()
            })

            function createJsonObj(result) {
                var geojsonObject = '{' +
                    '"type": "FeatureCollection",' +
                    '"crs": {' +
                    '"type": "name",' +
                    '"properties": {' +
                    '"name": "EPSG:4326"' +
                    '}' +
                    '},' +
                    '"features": [{' +
                    '"type": "Feature",' +
                    '"geometry": ' + result +
                    '}]' +
                    '}';
                return geojsonObject;
            }

            function drawGeoJsonObj(paObjJson) {
                var vectorSource = new ol.source.Vector({
                    features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    })
                });
                var vectorLayer = new ol.layer.Vector({
                    source: vectorSource
                });
                map.addLayer(vectorLayer);
            }

            function highLightGeoJsonObj(paObjJson) {
                var vectorSource = new ol.source.Vector({
                    features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    })
                });
                vectorLayer.setSource(vectorSource);

            }

            function highLightObj(result) {
                //alert("result: " + result);
                var strObjJson = createJsonObj(result);
                // alert(strObjJson);
                var objJson = JSON.parse(strObjJson);
                // alert(JSON.stringify(objJson));
                //drawGeoJsonObj(objJson);
                highLightGeoJsonObj(objJson);
            }

            function highlight_file(result, coordinate) {
                // var co=result.coordinates
                // console.log(co)
                console.log(result.type)

                console.log(item.type)
                var strObjJson = createJsonObj(item);
                // alert(strObjJson);
                var objJson = JSON.parse(strObjJson);
                // alert(JSON.stringify(objJson));
                //drawGeoJsonObj(objJson);
                highLightGeoJsonObj(objJson);

            }

            function displayObjInfo(result, coordinate) {
                $("#info").html(result);

            }

            function displayInfo(result, coordinate) {
                //alert("result: " + result);
                //alert("coordinate des: " + coordinate);
                $("#kq").html(result);
            }

            function displaySearch(result) {
                $(".searchResult").html(result);
            }

            function displaySearch2(result) {
                $("#kq_bv").html(result);

            }

            function dis() {

                $(".trId").click(function() {

                    a = $(this).html()

                    $.ajax({
                        type: "POST",
                        url: "pgsqlAPI.php",

                        data: {
                            id: a
                        },
                        success: function(result) {
                            highLightObj(result);
                            
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                })

            }

            $(".btnSearch").click(function() {
                if ($('#search').val() == "") {
                    $("#error").html("nhập tên bệnh viện cần tìm")
                } else {
                    var name = $('#search').val();
                    $("#error").html("")
                    $.ajax({
                        type: "POST",
                        url: "pgsqlAPI.php",

                        data: {
                            search: name
                        },
                        success: function(result) {
                            displaySearch(result);

                            dis();
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }

            })
           

            map.on('singleclick', function(evt) {
                vectorLayer.setStyle(styleFunction);
                // vectorLayer.setStyle(stylePoint);
                var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                var lon = lonlat[0];
                var lat = lonlat[1];
                var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                console.log(lonlat)

                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    data: {
                        functionname: 'getInfoCMRToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        displayObjInfo(result, evt.coordinate);



                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "pgsqlAPI.php",
                    //dataType: 'json',
                    data: {
                        functionname: 'getGeoCMRToAjax',
                        paPoint: myPoint
                    },
                    success: function(result, status, erro) {
                        highLightObj(result);
                    },
                    error: function(req, status, error) {
                        alert(req + " " + status + " " + error);
                    }
                });
            });

            document.getElementById('title-search').onclick = function(e) {
                if (this.checked) {
                    map.on('singleclick', function(evt) {

                       

                        var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');

                        var lon = lonlat[0];
                        var lat = lonlat[1];
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';


                        $.ajax({

                            type: "POST",
                            url: "pgsqlAPI.php",
                            //dataType: 'json',
                            data: {
                                functionname: 'getBv',
                                paPoint: myPoint
                            },
                            success: function(result, status, erro) {
                                displaySearch2(result)
                                $(".trId2").click(function() {

                                    b = $(this).html()


                                    $.ajax({
                                        type: "POST",
                                        url: "pgsqlAPI.php",

                                        data: {
                                            id: b
                                        },
                                        success: function(result) {
                                        highLightObj(result);
                                    },
                                        error: function(req, status, error) {
                                            alert(req + " " + status + " " + error);
                                        }
                                    });
                                    

                                    })


                            },
                            error: function(req, status, error) {
                                alert(req + " " + status + " " + error);
                            }
                        });

                    })
                } else {
                    location.reload();
                }


            };
        };
        //});
    </script>
</body>

</html>