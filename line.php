<script>
function legend () 
{
    $('#legend').empty();
    var no_layers = overlays.getLayers().get('length');
    var head = document.createElement("h4");
var txt = document.createTextNode("Legend");
  head.appendChild(txt);
  var element = document.getElementById("legend");
  element.appendChild(head);
    var ar = [];
    var i;
    for (i = 0; i < no_layers; i++) {
    ar.push("http://localhost:8080/geoserver/example/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER="+overlays.getLayers().item(i).get('title'));
//alert(overlays.getLayers().item(i).get('title'));
    }
    for (i = 0; i < no_layers; i++) 
{
    var head = document.createElement("p");
  var txt = document.createTextNode(overlays.getLayers().item(i).get('title'));
    //alert(txt[i]);
   head.appendChild(txt);
   var element = document.getElementById("legend");
   element.appendChild(head);
     var img = new Image();
   img.src = ar[i];
  
 var src = document.getElementById("legend");
  src.appendChild(img);
 }
 }
 
 legend();
 
  
 




    
    getinfotype.onchange = function() 
{
    map.removeInteraction(draw);
if (vectorLayer) {vectorLayer.getSource().clear();}
map.removeOverlay(helpTooltip);
    if (measureTooltipElement) {
    var elem = document.getElementsByClassName("tooltip tooltip-static");
    
    for(var i = elem.length-1; i >=0; i--)
      {

elem[i].remove();
//alert(elem[i].innerHTML);
      }     
        }
    
    if (getinfotype.value == 'activate_getinfo')
    {
      map.on('singleclick', getinfo);
    }
    else if (getinfotype.value == 'select' || getinfotype.value == 'deactivate_getinfo') 
    {
      map.un('singleclick', getinfo);
    overlay.setPosition(undefined);
          closer.blur();
    }
  };
    
    // measure tool
    
    var source = new ol.source.Vector();
var vectorLayer = new ol.layer.Vector({
//title: 'layer',
source: source,
style: new ol.style.Style({
      fill: new ol.style.Fill({
        color: 'rgba(255, 255, 255, 0.2)'
      }),
      stroke: new ol.style.Stroke({
        color: '#ffcc33',
        width: 2
      }),
      image: new ol.style.Circle({
        radius: 7,
        fill: new ol.style.Fill({
          color: '#ffcc33'
        })
      })
    })
  });

//overlays.getLayers().push(vectorLayer);
map.addLayer(vectorLayer);

//layerSwitcher.renderPanel();

    
    /**
   * Currently drawn feature.
   * @type {module:ol/Feature~Feature}
   */
  var sketch;


  /**
   * The help tooltip element.
   * @type {Element}
   */
  var helpTooltipElement;


  /**
   * Overlay to show the help messages.
   * @type {module:ol/Overlay}
   */
  var helpTooltip;


  /**
   * The measure tooltip element.
   * @type {Element}
   */
  var measureTooltipElement;


  /**
   * Overlay to show the measurement.
   * @type {module:ol/Overlay}
   */
  var measureTooltip;


  /**
   * Message to show when the user is drawing a polygon.
   * @type {string}
   */
  var continuePolygonMsg = 'Click to continue drawing the polygon';


  /**
   * Message to show when the user is drawing a line.
   * @type {string}
   */
  var continueLineMsg = 'Click to continue drawing the line';


  /**
   * Handle pointer move.
   * @param {module:ol/MapBrowserEvent~MapBrowserEvent} evt The event.
   */
  var pointerMoveHandler = function(evt) {
    if (evt.dragging) {
      return;
    }
    /** @type {string} */
    var helpMsg = 'Click to start drawing';

    if (sketch) {
      var geom = (sketch.getGeometry());
      if (geom instanceof ol.geom.Polygon) {
     
        helpMsg = continuePolygonMsg;
      } else if (geom instanceof ol.geom.LineString) {
        helpMsg = continueLineMsg;
      }
    }

    helpTooltipElement.innerHTML = helpMsg;
    helpTooltip.setPosition(evt.coordinate);

    helpTooltipElement.classList.remove('hidden');
  };
  
   map.on('pointermove', pointerMoveHandler);

  map.getViewport().addEventListener('mouseout', function() {
    helpTooltipElement.classList.add('hidden');
  });

  //var measuretype = document.getElementById('measuretype');

  var draw; // global so we can remove it later


  /**
   * Format length output.
   * @param {module:ol/geom/LineString~LineString} line The line.
   * @return {string} The formatted length.
   */
  var formatLength = function(line) {
  var length = ol.sphere.getLength(line,{projection:'EPSG:4326'});
    //var length = getLength(line);
    //var length = line.getLength({projection:'EPSG:4326'});
    
    var output;
    if (length > 100) {
      output = (Math.round(length / 1000 * 100) / 100) +
          ' ' + 'km';
          
    } else {
      output = (Math.round(length * 100) / 100) +
          ' ' + 'm';
          
    }
    return output;
    
  };


  /**
   * Format area output.
   * @param {module:ol/geom/Polygon~Polygon} polygon The polygon.
   * @return {string}// Formatted area.
   */
  var formatArea = function(polygon) {
 // var area = getArea(polygon);
 var area = ol.sphere.getArea(polygon, {projection:'EPSG:4326'});
   // var area = polygon.getArea();
    //alert(area);
    var output;
    if (area > 10000) {
      output = (Math.round(area / 1000000 * 100) / 100) +
          ' ' + 'km<sup>2</sup>';
    } else {
      output = (Math.round(area * 100) / 100) +
          ' ' + 'm<sup>2</sup>';
    }
    return output;
  };
