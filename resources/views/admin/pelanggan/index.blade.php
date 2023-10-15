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
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-user"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jobs Berjalan</h4>
                            </div>
                            <div class="card-body">
                                {{ $jobs }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <a href="/logs" target="_blank">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="far fa-newspaper"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Jobs Gagal</h4>
                                </div>
                                <div class="card-body">
                                    {{ $failed_jobs }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="far fa-file"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total User</h4>
                            </div>
                            <div class="card-body">
                                {{ $total_user }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Data Pelanggan</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-info mr-2" onclick="add()"><i class="fa fa-sync mr-1"></i>Sync pelanggan by ID Partner</button>
                        <button type="button" class="btn btn-warning mr-2" onclick="upload_excel_modal()"><i class="fa fa-sync mr-1"></i>Excel Relational Sales - Pelanggan</button>
                        {{-- <button type="button" class="btn btn-success mr-2" onclick="sync_pelanggan()"><i class="fa fa-sync mr-1"></i>Sync Pelanggan</button> --}}
                        <button type="button" hidden class="btn btn-warning" onclick="window.location.href = '/admin/pelanggan/add';"><i class="fa fa-plus mr-1"></i>Tambah Pelanggan</button>
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
                                    <th scope="col" class="text-center">Status</th>
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

@section('modal')
<div class="modal fade" role="dialog" id="modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
       <div class="modal-content">
          <div class="modal-header br">
             <h5 class="modal-title"></h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
             </button>
          </div>
          <form id="form_submit_request" action="/api/odoo/sync-pelanggan-by-id" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Partner ID</label>
                              <input class="form-control" type="text" id="partner_id" name="partner_id">
                              <small>Partner ID harus dipisahkan dengan sparator "|"</small>
                              <br>
                              <small>contoh -> 83376|46103</small>
                        </div>
                    </div>
                </div>
             </div>
             <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning">Simpan</button>
             </div>
          </form>
       </div>
    </div>
</div>

<!-- Modal Upload -->
<form id="form_upload" action="/admin/pelanggan/upload-excel" method="post" enctype="multipart/form-data">
    <div class="modal fade" role="dialog" id="modal_upload" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header br">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 style="color:black">Petunjuk</h6>
                    <p>Format susunan file excel, sebagai berikut :</p>

                    <ul>
                    <li>Ketentuan 1</li>
                    <li>Ketentuan 2</li>
                    <li>Ketentuan 3</li>
                    </ul>

                    <a href="{{asset('/berkas/template-excel/relational_sales_pelanggan.xlsx')}}" class="btn btn-success"><i class="fas fa-download"></i> Download Template Excel</a>
                </div>

                <div class="col-md-6">
                    <div class="text-center">
                        <span>
                            <i  style="font-size: 30px; padding-bottom: 20px;" class="fa fa-upload"></i>
                        </span>
                        <p style="line-height: 10px;">Silahkan pilih file excel yang akan diupload</p>
                        <label class="label-upload" for="file_excel">
                            <input class="input-upload" type="file" name="file_excel" id="file_excel" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .xls" required="" onchange="get_path_file();">Upload File
                        </label>
                        <small id="path_file_text"></small>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal-footer bg-whitesmoke br">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
        </div>
    </div>
    </div>
</form>
<!-- Modal Upload -->
@endsection

@section('js')
<script src="{{ asset('assets/vendors/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('assets/vendors/leaflet/leaflet.markercluster.js') }}"></script>
<script>

function upload_excel_modal(){
        $("#modal_upload").modal('show');
        $(".modal-title").text('Upload Excel');
    }

    function add(){
        $("#modal").modal('show');
        $(".modal-title").text('Tambah Partner by ID');
        $("#form_submit_request")[0].reset();
        reset_all_select();
    }

    function edit(url){
        edit_action(url, 'Edit Partner by ID');
        $("#type").val('update');
    }

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
            {   data: 'status_badge'     },
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

            let action = `
                <button class="btn btn-info btn-sm mr-1" onclick="window.open('${url_edit}', '_blank');"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-sm mr-1" onclick="delete_action('${url_delete}','${data.nama}')"><i class="fa fa-trash"> </i></button>
            `;

            if(data.is_lock_location == 1){
                action += `<button class="btn btn-success btn-sm mr-1" onclick="action_lock('${data.id}','0')"><i class="fa fa-lock-open mr-2"></i>Unlock</button>`;
            }else{
                action += `<button class="btn btn-warning btn-sm mr-1" onclick="action_lock('${data.id}','1')"><i class="fa fa-lock mr-2"></i>lock</button>`;
            }

            if(data.status_badge == `<span class='badge badge-danger'>inactive</span>`){
                action += `<button class="btn btn-primary btn-sm" onclick="action('${data.id}')"><i class="fa fa-user mr-2"></i>Jadikan Sales</button>`;
            }

            $('td:eq(7)', row).html(action);
        }
    });

    function action_lock(id, status){

        let text = (status == 0) ? 'unlock' : 'lock';

        swal({
             title: 'Yakin?',
             text: 'Apakah anda yakin akan melakukan ' + text + ' edit lokasi pelanggan ini?',
             icon: 'warning',
             buttons: true,
             dangerMode: true,
       })
       .then((willDelete) => {
             if (willDelete) {
                $("#modal_loading").modal('show');
                $.ajax({
                    url: '/admin/pelanggan/lock-lokasi/' + id,
                    type: "GET",
                    dataType: 'JSON',
                    success: function( response, textStatus, jQxhr ){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        if(response.status == 200){
                            iziToast.success({
                                title: 'Success!',
                                message: response.message,
                                position: 'topRight'
                                });
                            tb.ajax.reload(null, false);
                        }else{
                            iziToast.error({
                                title: 'Error!',
                                message: response.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        console.log( errorThrown );
                        console.warn(jqXhr.responseText);
                    },
                });
            }action
        });
    }

    function action(id){
        swal({
             title: 'Yakin?',
             text: 'Apakah anda yakin akan menjadikan partner ini sales?',
             icon: 'warning',
             buttons: true,
             dangerMode: true,
       })
       .then((willDelete) => {
             if (willDelete) {
                $("#modal_loading").modal('show');
                $.ajax({
                    url: '/admin/pelanggan/jadikan-sales/' + id,
                    type: "GET",
                    dataType: 'JSON',
                    success: function( response, textStatus, jQxhr ){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        if(response.status == 200){
                            iziToast.success({
                                title: 'Success!',
                                message: response.message,
                                position: 'topRight'
                                });
                            tb.ajax.reload(null, false);
                        }else{
                            iziToast.error({
                                title: 'Error!',
                                message: response.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        console.log( errorThrown );
                        console.warn(jqXhr.responseText);
                    },
                });
            }action
        });
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
        swal({
             title: 'Yakin?',
             text: 'Apakah anda yakin akan sync data pelanggan?',
             icon: 'warning',
             buttons: true,
             dangerMode: true,
       })
       .then((willDelete) => {
             if (willDelete) {
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
        })
    }

</script>
@endsection
