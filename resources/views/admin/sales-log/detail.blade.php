@extends('partial.app')
@section('title','Sales Log')
@section('css')
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/leaflet.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/MarkerCluster.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/MarkerCluster.Default.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/modules/leaflet-routing-machine/leaflet-routing-machine.css') }}" />
   <style>
      .map-scroll:before {
         content: 'Use ctrl + scroll to zoom the map';
         position: absolute;
         top: 50%;
         left: 36%;
         z-index: 9999;
         font-size: 24px;
         color: #fff;
      }

      .map-scroll:after {
         position: absolute;
         left: 0;
         right: 0;
         bottom: 0;
         top: 0;
         content: '';
         background: rgba(0, 0, 0, 0.3);
         z-index: 998;
      }
   </style>
@endsection

@section('content')

<div class="section-body">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Sales Log</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-primary" onclick="reset_zoom();">Reset Zoom</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="myMap" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tb" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col" class="text-center">Latitude</th>
                                    <th scope="col" class="text-center">Longitude</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/vendors/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('assets/vendors/leaflet/leaflet.markercluster.js') }}"></script>
<script src="{{ asset('assets/modules/leaflet-routing-machine/leaflet-routing-machine.js') }}"></script>

<script>

    $(document).ready(function(){
        list_data();
    });

    var tb = $('#tb').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/admin/sales-log/detail-datatables/" + {{ $id }},
            type: 'GET'
        },
        columnDefs: [{
            className: 'text-center',
            targets: [0,1,2]
        }, ],
        columns: [{
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false},
            {   data: 'lat'},
            {   data: 'lng'},
        ]
    });
    
    var basemaps= {
        "Google Satelite":L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution:'Map data &copy; Google | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>'
        }),
        "Esri World Dark":L.tileLayer('https://server.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>',
        }),
        "OSM":L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }),
        "Esri World Gray":L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>',
        maxZoom: 16
        }),
        "Google Street":L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution:'Map data &copy; Google | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>'
        }),
        "Google Hybrid":L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution:'Map data &copy; Google | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>'
        }),
        "Google Traffic":L.tileLayer('https://{s}.google.com/vt/lyrs=m@221097413,traffic&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        min5oom: 2,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution:'Map data &copy; Google | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>'
        }),
        "Google Terrain":L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution:'Map data &copy; Google | Map By <a href="https://idraxy.web.app" target="_blank">Draxgist & Team</a>'
        }),
        "CYL" : L.tileLayer('https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        })
    }

   var map = L.map('myMap', {
        center: [-7.320063,112.7292863],
        zoom: 3,
        preferCanvas:true,
        bounceAtZoomLimits:false,
        minZoom: 5,
        zoomControl:false,
        layers: [
                basemaps["Google Satelite"]
        ],
    });

    //disable default scroll 
    map.scrollWheelZoom.disable();

    $("#myMap").bind('mousewheel DOMMouseScroll', function (event) {
    event.stopPropagation();
        if (event.ctrlKey == true) {
            event.preventDefault();
        map.scrollWheelZoom.enable();
            $('#myMap').removeClass('map-scroll');
        setTimeout(function(){
            map.scrollWheelZoom.disable();
        }, 1000);
        } else {
        map.scrollWheelZoom.disable();
        $('#myMap').addClass('map-scroll');
        }
    });

    $(window).bind('mousewheel DOMMouseScroll', function (event) {
        $('#myMap').removeClass('map-scroll');
    });

    $(document).mousemove(function(e){
        $('#myMap').removeClass('map-scroll');
    });

    function reset_zoom(){
        map.flyTo([-5.1114857,119.3325868], 5);
    }

    function list_data(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/admin/sales-log/detail-data/" + {{ $id }},
            type: "GET",
            dataType: "JSON",
            success: function(response){
               setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                var data = response.data;
                if(response.status === 200){
                    let arr = new Array();
                    if(data.length == 1){
                        iziToast.error({
                            title: 'Error!',
                            message: 'Not enough coordinates to create a route',
                            position: 'topRight'
                        });
                    }
                    for(var i = 0; i  < data.length; i++ ){
                        if(data[i].lat != null){
                            arr.push(new L.LatLng(data[i].lat, data[i].lng));
                            // routingControl.setWaypoints([
                            //     L.latLng(data[i].lat, data[i].lng)
                            // ])
                            // L.Routing.control({waypoints:[data[i].lat, data[i].lng]}).addTo(map);
                        }
                    }
                    routingControl = L.Routing.control({
                        waypoints: arr,
                        routeWhileDragging: false,
                        addWaypoints: false,
                        draggableWaypoints: false,
                        createMarker: function() { return null; }
                    }).addTo(map);
                }else{
                    swal(response.message, {  icon: 'error', });
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
               setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
               swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
        });
    }
</script>
@endsection
