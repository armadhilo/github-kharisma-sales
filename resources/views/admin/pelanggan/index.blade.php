@extends('partial.app')
@section('title','Pelanggan')
@section('css')
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/leaflet.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/MarkerCluster.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/MarkerCluster.Default.css') }}" />
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
            <div class="card" id="card-peta">
                <div class="card-header">
                    <h4>Lokasi Pelanggan</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-primary" onclick="reset_zoom();">Reset Zoom</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="myMap" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Data Pelanggan</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-success mr-2" onclick="sync_pelanggan()"><i class="fa fa-sync mr-1"></i>Sync Pelanggan</button>
                        <button type="button" class="btn btn-warning" onclick="window.location.href = '/admin/pelanggan/add';"><i class="fa fa-plus mr-1"></i>Tambah Pelanggan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tb" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col" class="text-center">Nama</th>
                                    <th scope="col" class="text-center">Nama Toko</th>
                                    <th scope="col" class="text-center">Email</th>
                                    <th scope="col" class="text-center">Telepon</th>
                                    <th scope="col" class="text-center">Lokasi</th>
                                    <th scope="col" class="text-center">Alamat</th>
                                    <th scope="col" class="text-center" style="width: 15%">Actions</th>
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
<script>
    var tb = $('#tb').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/pelanggan/datatables',
            type: 'GET'
        },
        columnDefs: [{
            className: 'text-center',
            targets: [0, 4, 5, 6, 7]
        }, ],
        columns: [{
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false},
            {   data: 'nama'     },
            {   data: 'nama_toko'},
            {   data: 'email'    },
            {   data: 'telepon'  },
            {   data: 'kota'     },
            {   data: 'alamat'     },
            {   data: 'kota'     },
        ],
        rowCallback: function (row, data) {
            if(data.lat === null){
               $('td:eq(5)', row).html(`<a href="javascript:void(0)"><b>Latitude</b> : - <br> <b>Longitude</b> : - </a>`);

            }else{
               $('td:eq(5)', row).html(`<a href="javascript:void(0)" onclick="get_lang_lat('${data.lat}', '${data.lng}');"><b>Latitude</b> : ${data.lat} <br> <b>Longitude</b> : ${data.lng} </a>`);
            }

            var url_edit = "/admin/pelanggan/detail/" + data.id;
            var url_delete = "/admin/pelanggan/delete/" + data.id;
            $('td:eq(7)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="window.open('${url_edit}', '_blank');"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="delete_action('${url_delete}','${data.nama}')"><i class="fa fa-trash"> </i></button>
            `);
        }
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

   $(document).ready(function(){
      list_pelanggan();
   });

   var map = L.map('myMap', {
            // center: [-7.320063,112.7292863],
            center: [-5.1114857,119.3325868],
            zoom: 2,
            preferCanvas:true,
            bounceAtZoomLimits:false,
            minZoom: 5,
                  // maxBounds:[
                  //    [-9.319,110.868],
                  //    [-5.713,116.103]
                  // ],
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

      function get_lang_lat(lat, lng){
        // map.flyTo(new L.LatLng(lat, lng));
        map.flyTo([lat, lng], 18);
        $("#card-peta").attr("tabindex",-1).focus();
      }

    function list_pelanggan(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/admin/master/get-data-where-field-id_get/User/type/2",
            type: "GET",
            dataType: "JSON",
            success: function(response){
               setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                var data = response.data;
               if(response.status === 200){
                  for(var i = 0; i  < data.length; i++ ){
                     if(data[i].lat != null){
                        var buildingIcon = L.icon({
                           iconUrl: "{{ asset('assets/img/project/icon/icon-marker.png') }}",
                           iconSize: [25, 32],
                        });
                        
                        L.marker([data[i].lat, data[i].lng], {icon: buildingIcon}).addTo(map).bindPopup(`Nama Pelanggan : <b>${data[i].nama}</b><br>Nama Toko : <b>${data[i].nama_toko}</b><br> Alamat : <b>${data[i].alamat}</b> <br> Telepon : <b>${data[i].telepon}</b>` );
                     }
                  }
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

    function sync_pelanggan(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/api/odoo/sync-pelanggan",
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.status === 200){
                    swal(response.message, {  icon: 'success', });
                }else{
                    swal(response.message, {  icon: 'error', });
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
        });
    }
    
</script>
@endsection
