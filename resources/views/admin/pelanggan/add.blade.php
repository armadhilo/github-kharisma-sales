@extends('partial.app')
@section('title','Data Pelanggan')

@section('css')
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/leaflet.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/MarkerCluster.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/leaflet/MarkerCluster.Default.css') }}" />
   <link rel="stylesheet" href="{{ asset('assets/vendors/esri-leaflet/esri-leaflet-geocoder.css') }}">
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

      .select2.is-invalid .select2-selection .select2-selection__rendered{
         border: 1px solid #dc3545!important;
      }
   </style>
@endsection

@section('content')

<div class="section-body">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-header">
               <h4>Tambah Data Pelanggan</h4>
            </div>
            <form autocomplete="off" id="form_submit_request" action="/admin/pelanggan/store-update" method="POST">
                @if(isset($data))
                @method('PUT')
                @endif
               <div class="card-body pt-0">
                  <div class="row">
                     <div class="col-12 col-md-5 col-lg-5">
                        <div class="form-group">
                           <label>Nama</label>
                           <input type="text" hidden class="form-control" value="@if(isset($data)){{$data->id}}@endif" name="id" id="id">
                           <input type="text" name="nama" id="nama" value="@if(isset($data)) {{$data->nama}} @endif" class="form-control">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-nama-feedback"></span>
                        </div>
                     </div>
                     <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group">
                           <label>Email</label>
                           <input type="text" name="email" id="email" value="@if(isset($data)) {{$data->email}} @endif" class="form-control">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-email-feedback"></span>
                        </div>
                     </div>
                     <div class="col-12 col-md-3 col-lg-3">
                        <div class="form-group">
                           <label>Telepon</label>
                           <input type="text" name="telepon" id="telepon" value="@if(isset($data)) {{$data->telepon}} @endif" class="form-control" onkeypress="return hanyaAngka(event,false);">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-telepon-feedback"></span>
                        </div>
                     </div>
                     @if(isset($data))
                     <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group">
                           <div class="d-flex justify-content-between">
                                 <label style="white-space: nowrap">Change Password</label>
                                 <span class="invalid-feedback float-right d-block" style="text-align: end">*Tidak Perlu Diisi Bila Password Tidak Diubah</span>
                           </div>
                           <input type="text" name="password" id="password" class="form-control">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-password-feedback"></span>
                        </div>
                     </div>
                     @else
                     <div class="col-12 col-md-3 col-lg-3">
                        <div class="form-group">
                           <label style="white-space: nowrap">Password</label>
                           <input type="text" name="password" id="password" class="form-control">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-password-feedback"></span>
                        </div>
                     </div>
                     <div class="col-12 col-md-3 col-lg-3">
                        <div class="form-group">
                           <label style="white-space: nowrap">Retype Password</label>
                           <input type="text" name="retype_password" id="retype_password" class="form-control">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-retype_password-feedback"></span>
                        </div>
                     </div>
                     @endif
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group">
                           <label>Nama Toko</label>
                           <input type="text" name="nama_toko" id="nama_toko" value="@if(isset($data)) {{$data->nama_toko}} @endif" class="form-control">
                           <span class="d-flex text-danger invalid-feedback" id="invalid-nama_toko-feedback"></span>
                        </div>
                     </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group">
                           <label>Alamat</label>
                           <textarea class="form-control" style="height: 90px" name="alamat">@isset($data->alamat) {{$data->alamat}} @endisset</textarea>
                           <span class="d-flex text-danger invalid-feedback" id="invalid-alamat-feedback"></span>
                        </div>
                     </div>
                     <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group">
                           <label>Alamat Toko</label>
                           <textarea class="form-control" style="height: 90px" name="alamat_toko">@isset($data->alamat_toko) {{$data->alamat_toko}} @endisset</textarea>
                           <span class="d-flex text-danger invalid-feedback" id="invalid-alamat_toko-feedback"></span>
                        </div>
                     </div>
                     <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group">
                            <label>Kota</label>
                            <input hidden type="text" id="data_kota" class="form-control" value="@if(isset($data)){{$data->kota}}@endif">
                            <select class="form-control select2" name="kota" onchange="change_kota()" id="kota">
                                <option selected disabled>- Pilih -</option>
                            </select>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-kota-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <input hidden type="text" id="data_kecamatan" class="form-control" value="@if(isset($data)){{$data->kecamatan}}@endif">
                            <select class="form-control select2" name="kecamatan" onchange="change_kecamatan()" id="kecamatan" @if(!isset($data->kecamatan)){{ 'disabled' }}@endif>
                                <option selected disabled>- Pilih -</option>
                            </select>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-kecamatan-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="form-group">
                            <label>Kelurahan</label>
                            <input hidden type="text" id="data_kelurahan" class="form-control" value="@if(isset($data)){{$data->kelurahan}}@endif">
                            <select class="form-control select2" name="kelurahan" id="kelurahan" @if(!isset($data->kelurahan)){{ 'disabled' }}@endif>
                                <option selected disabled>- Pilih -</option>
                            </select>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-kelurahan-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 col-lg-5">
                        <div class="form-group">
                           <label>Latitude</label>
                           <input type="text" name="lat_search" id="lat_search" class="form-control">
                        </div>
                     </div>
                     <div class="col-12 col-md-5 col-lg-5">
                        <div class="form-group">
                           <label>Longitude</label>
                           <input type="text" name="lng_search" id="lng_search" class="form-control">
                        </div>
                     </div>
                     <div class="col-12 col-md-1 col-lg-1 mt-2">
                        <label></label>
                        <button class="btn btn-primary" id="button_search_longlat">Search</button>
                     </div>
                     <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                           <label>Koordinat Lokasi</label>
                           <div id="myMap" style="width: 100%; height: 400px;"></div>
                        </div>
                     </div>
                     <div class="col-12 col-md-3 col-lg-3">
                        <div class="form-group">
                           <label>Latitude</label>
                           <input type="text" name="lat" id="lat" value="@if(isset($data)) {{$data->lat}} @endif" class="form-control" readonly>
                           <span class="d-flex text-danger invalid-feedback" id="invalid-lat-feedback"></span>
                        </div>
                     </div>
                     <div class="col-12 col-md-3 col-lg-3">
                        <div class="form-group">
                           <label>Longitude</label>
                           <input type="text" name="lng" id="lng" value="@if(isset($data)) {{$data->lng}} @endif" class="form-control" readonly>
                           <span class="d-flex text-danger invalid-feedback" id="invalid-lng-feedback"></span>
                        </div>
                     </div>
                  </div>
               </div>
               @if(Auth::user()->role != 4)
               <div class="card-footer text-right">
                  <button type="submit" class="btn btn-success"><i class="fa fa-save m-1"></i> Save</button>
               </div>
               @endif
            </form>
         </div>
     </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/vendors/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('assets/vendors/leaflet/leaflet.markercluster.js') }}"></script>
<script src="{{ asset('assets/vendors/esri-leaflet/esri-leaflet.js') }}"></script>
<script src="{{ asset('assets/vendors/esri-leaflet/esri-leaflet-geocoder.js') }}"></script>
<script>
   function alphaOnly(a) {
      var b = '';
      for (var i = 0; i < a.length; i++) {
         if (a[i] >= 'A' && a[i] <= 'z') b += a[i];
      }
      return b;
   }
  
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
      zoom: 15,
      preferCanvas:true,
      bounceAtZoomLimits:false,
      minZoom: 8,
            // maxBounds:[
            //    [-9.319,110.868],
            //    [-5.713,116.103]
            // ],
      zoomControl:false,
      layers: [
            basemaps["Google Street"]
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
   })
   
   $(document).mousemove(function(e){
      $('#myMap').removeClass('map-scroll');
   });
   
   var searchControl = new L.esri.Controls.Geosearch().addTo(map);
   var results = new L.LayerGroup().addTo(map);
   searchControl.on('results', function(data){
      results.clearLayers();
      for (var i = data.results.length - 1; i >= 0; i--) {
            $("#lat").val(data.results[i].latlng.lat);
            $("#lng").val(data.results[i].latlng.lng);
            results.addLayer(L.marker(data.results[i].latlng));
      }
   });
   var marker = null;
   var groupCircle = null;
   map.on("click", function(e){
         results.clearLayers();
         if (marker !== null) {
            map.removeLayer(marker);
         }
         marker = new L.Marker(e.latlng, {draggable:true});
         results.addLayer(marker);
         
         // results.addLayer(L.marker(e.latlng));
         $("#lat").val(e.latlng.lat);
         $("#lng").val(e.latlng.lng);

         if (groupCircle !== null) {
            map.removeLayer(groupCircle);
         }
         $("#radius").val(50)
         groupCircle = new L.circle([e.latlng.lat,e.latlng.lng], 50)
         map.addLayer(groupCircle);
   });

   $(function() {
      $(".numonly").on('input', function(e) {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
      });
   });

   $(document).ready(function(){
      $('#radius').on('keyup', function(){
        
         if($(this).val() != ''){
            var radius = parseInt($(this).val());
            if (groupCircle !== null) {
               map.removeLayer(groupCircle);
            }
            
            groupCircle = new L.circle([$("#lat").val(), $("#lng").val()], radius)
            map.addLayer(groupCircle);
         }
      })

      get_kota();
      var latitude         = $('#lat').val();
      var longitude        = $('#lng').val();
      var nama_perusahaan  = $('#nama').val();
      var alamat           = $('#alamat').val();
      marker = new L.marker([latitude, longitude]).addTo(map).bindPopup("<b>" + nama_perusahaan + "<b><br>" + alamat);
      if(latitude != ""){
         map.flyTo(new L.LatLng(latitude, longitude), 13);
      }

      if($("#role").val() == 4){
         $('.form-control').each(function(){
            $(this).attr("disabled", true);
         });
      }

      $("#nama_singkat").keyup(function(){
         var valUpper = this.value.toUpperCase();
         $("#nama_singkat").val(valUpper);
      });
      
   });

   $('#button_search_longlat').click(function(e){
      e.preventDefault();

      var latitude         = $('#lat_search').val();
      var longitude        = $('#lng_search').val();

     if (marker !== null) {
        map.removeLayer(marker);
     }

     $('#lat').val(latitude);
     $('#lng').val(longitude);

     marker = new L.marker([latitude, longitude]).addTo(map);
     map.flyTo(new L.LatLng(latitude, longitude), 13);
   });

    function get_kota(){
       $("#modal_loading").modal('show');
        $.ajax({
            url : "/api/kabkota/53",
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                var data_kota = $("#data_kota").val();
                $("#kota").empty();
                $("#kota").append(`<option selected disabled>- Pilih -</option>`);
                for (var i = 0; i < response.length; i++) {
                    if(data_kota == response[i]['id']){
                        $("#kota").append(`<option value="${response[i]['id']}" selected>${response[i]['nama']}</option>`);
                        change_kota();
                    }else{
                        $("#kota").append(`<option value="${response[i]['id']}">${response[i]['nama']}</option>`);
                    }
                }
            },error: function (jqXHR, textStatus, errorThrown){
                swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
        });
    }

    function change_kota(){
       $("#modal_loading").modal('show');
        $.ajax({
            url : "/api/kecamatan/" + $('#kota').val(),
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                var data_kecamatan = $("#data_kecamatan").val();
                $("#kecamatan").empty();
                $('#kecamatan').append('<option selected disabled>- Pilih -</option>');
                $('#kecamatan').prop('disabled', false);
                for (var i = 0; i < response.length; i++) {
                  if(data_kecamatan == response[i]['id']){
                     $("#kecamatan").append(`<option value="${response[i]['id']}" selected>${response[i]['nama']}</option>`);
                     change_kecamatan();
                  } else {
                     $("#kecamatan").append(`<option value="${response[i]['id']}">${response[i]['nama']}</option>`);
                  }
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                 Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
            }
        });
    }

    function change_kecamatan(){
       $("#modal_loading").modal('show');
        $.ajax({
            url : "/api/kelurahan/" + $('#kecamatan').val(),
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                var data_kelurahan = $("#data_kelurahan").val();
                $("#kelurahan").empty();
                $('#kelurahan').append('<option selected disabled>- Pilih -</option>');
                $('#kelurahan').prop('disabled', false);
                for (var i = 0; i < response.length; i++) {
                if(data_kelurahan == response[i]['id']){
                  $("#kelurahan").append(`<option value="${response[i]['id']}" selected>${response[i]['nama']}</option>`);
                } else {
                  $("#kelurahan").append(`<option value="${response[i]['id']}">${response[i]['nama']}</option>`);
                }
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                 Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
            }
        });
    }

</script>
@endsection
