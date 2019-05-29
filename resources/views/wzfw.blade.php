<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
   <title></title>
   <script src="/ui/js/mui.min.js"></script>
   <link href="/ui/css/mui.min.css" rel="stylesheet"/>
   <link rel="stylesheet" type="text/css" href="/ui/css/main.css"/>
   <link rel="stylesheet" href="/ui/css/index.css">
   <link rel="stylesheet" type="text/css" href="/ui/css/theme.css"/>
   <script type="text/javascript" charset="utf-8">
      mui.init();
   </script>
   <link rel="stylesheet" type="text/css" href="/Ips_api_javascript/dijit/themes/tundra/tundra.css"/>
   <link rel="stylesheet" type="text/css" href="/Ips_api_javascript/esri/css/esri.css" />
   <link rel="stylesheet" type="text/css" href="/Ips_api_javascript/fonts/font-awesome-4.7.0/css/font-awesome.min.css" />
   <link rel="stylesheet" type="text/css" href="/Ips_api_javascript/Ips/css/widget.css" />
   <script type="text/javascript" src="/Ips_api_javascript/init.js"></script>
</head>
<body>
<style>
   html, body, #map_wzfw {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      z-index: 100;
   }
   #start {
      position: absolute;bottom:3%;left:20%;font-size: 18px;z-index: 100;
   }
   #stop {
      position: absolute;bottom:3%;left:40%;font-size: 18px;z-index: 100;
   }
   #analysis{
      position: absolute;bottom:3%;left:60%;font-size: 18px;z-index: 100;
   }
</style>
<header class="mui-bar mui-bar-nav theme-bgcolor">
   <h1 class="mui-title white-color">
      <div id="segmentedControl" class="mui-segmented-control mui-segmented-control-inverted mui-segmented-control-primary">
         <a class="" href="#">
            <b>位置服务</b>
         </a>
         <a class="" href="{{ url('aqjk') }}">
            安全监控
         </a>
         <a class="" href="{{ url('yjjy') }}">
            应急救援
         </a>
      </div>
   </h1>
</header>

<div class="mui-content" >
   <div class="mui-input-row mui-search mui-input-speech index-search theme-bgcolor" style="position: relative;z-index: 1000">
      <input type="search" class="mui-input-clear" placeholder="搜索">
   </div>
</div>
{{--位置服务布局代码--}}
<div id="map_wzfw" class="">
</div>
<button id="start" style="position: absolute">起点</button>
<button id="stop">终点</button>
<button id="analysis">分析</button>

<script>
   var map_wzfw;
   require([
      "Ips/map",
      "Ips/layers/DynamicMapServiceLayer",
      "esri/graphic",
      "Ips/symbol/SimpleMarkerSymbol",
      "Ips/symbol/TextSymbol",
      "esri/symbols/SimpleLineSymbol",
      "esri/tasks/RouteParameters",
      "esri/tasks/RouteTask",
      "esri/tasks/FeatureSet",
      "dojo/colors",
      "dojo/on",
      "dojo/dom",
      "dojo/domReady!"
   ], function (Map,DynamicMapServiceLayer,Graphic,SimpleMarkerSymbol,TextSymbol,SimpleLineSymbol,
                RouteParameters,RouteTask,FeatureSet,Color,on,dom){
      map_wzfw = new Map("map_wzfw", {
         logo:false
      });
      //初始化F1楼层平面图
      var f1 = new DynamicMapServiceLayer("http://121.28.103.199:5567/arcgis/rest/services/331/floorone/MapServer");
      map_wzfw.addLayer(f1);

      //创建路径分析对象
      var routeAnalyst = new RouteTask("http://121.28.103.199:5567/arcgis/rest/services/331/network1/NAServer/route");
      //创建路径参数对象
      var routeParas = new RouteParameters();
      //障碍点，但是此时障碍点为空
      routeParas.barriers = new FeatureSet();
      //停靠点，但是此时停靠点为空
      routeParas.stops = new FeatureSet();
      //路径是否有方向
      routeParas.returnDirections = false;
      //是否返回路径，此处必须返回
      routeParas.returnRoutes = true;
      //空间参考
      routeParas.outSpatialReference = map_wzfw.SpatialReference;

      var selectStartPointID;
      var selectStopPointID;
      //给停靠点按钮添加点击事件
      on(dom.byId("start"),"click",function(){
         selectStartPointID = 1;
      });
      on(dom.byId("stop"),"click",function(){
         selectStopPointID = 1;
      });

      //定义停靠点的符号
      var stopSymbol = new SimpleMarkerSymbol();
      stopSymbol.style = SimpleMarkerSymbol.STYLE_CIRCLE;
      stopSymbol.setSize(8);
      stopSymbol.setColor(new Color("#ffc61a"));

      on(map_wzfw, "click", function(evt){
         if(selectStartPointID==1){
            //获得停靠点的坐标
            var pointStart=evt.mapPoint;
            var gr=new Graphic(pointStart,stopSymbol);
            //构建停靠点的参数
            routeParas.stops.features.push(gr);

            //如果selectStartPointID不等于0，将点的坐标在地图上显示出来
            if (selectStartPointID != 0) {
               addTextPoint("起点", pointStart, stopSymbol);

               selectStartPointID = 0;
            }
         }
      });
      on(map_wzfw, "click", function(evt){
         if(selectStopPointID==1){
            //获得停靠点的坐标
            var pointStop=evt.mapPoint;
            var gr=new Graphic(pointStop,stopSymbol);
            //构建停靠点的参数
            routeParas.stops.features.push(gr);

            //如果selectStopPointID不等于0，将点的坐标在地图上显示出来
            if (selectStopPointID != 0) {
               addTextPoint("终点", pointStop, stopSymbol);

               selectStopPointID = 0;
            }
         }
      });
      //文本符号：文本信息，点坐标，符号
      function addTextPoint(text,point,symbol) {
         var textSymbol = new TextSymbol(text);
         textSymbol.setColor(new Color([128, 0, 0]));
         var graphicText = Graphic(point, textSymbol);
         var graphicpoint = new Graphic(point, symbol);
         //用默认的图层添加
         map_wzfw.graphics.add(graphicpoint);
         map_wzfw.graphics.add(graphicText);
      }
      //给分析按钮添加点击事件
      on(dom.byId("analysis"),"click",function(){
         //如果障碍点或者停靠点的个数有一个为0，提示用户参数输入不对
         if  (routeParas.stops.features.length == 0)
         {
            alert("输入参数不全，无法分析");
            return;
         }
         //执行路径分析函数
         routeAnalyst.solve(routeParas, showRoute)
      })
      //处理路径分析返回的结果。
      function showRoute(solveResult) {
         //路径分析的结果
         var routeResults = solveResult.routeResults;
         //路径分析的长度
         var res = routeResults.length;
         //路径的符号
         var routeSymbol  = new SimpleLineSymbol(SimpleLineSymbol.STYLE_DASH, new Color([0, 0, 200]), 3);
         if (res > 0) {
            for (var i = 0; i < res; i++) {
               var graphicroute = routeResults[i];
               var graphic = graphicroute.route;
               graphic.setSymbol(routeSymbol);
               map_wzfw.graphics.add(graphic);
            }
         }
         else {
            alert("没有返回结果");
         }
      }
   });
</script>
</body>
</html>
