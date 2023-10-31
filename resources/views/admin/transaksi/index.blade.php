@extends('partial.app')
@section('title','Transaksi')

@section('content')
<div class="card">
    <div class="card-body">
        <ul class="nav nav-pills" id="myTab3" role="tablist">
            <li class="nav-item mx-3">
                <a class="nav-link active position-relative" id="semua-tab" data-toggle="tab" href="#semua" role="tab"
                    aria-controls="semua" aria-selected="true">
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light"
                        style="top: -10px; right: -17px" id="place_count_semua">{{ $count_semua }}</span>
                    Semua
                </a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link position-relative" id="konfirmasiAdmin-tab" data-toggle="tab"
                    href="#konfirmasiAdmin" role="tab" aria-controls="konfirmasiAdmin" aria-selected="false">
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light"
                        style="top: -10px; right: -17px;" id="place_count_konfirmasi_admin">{{ $count_konfirmasi_admin }}</span>
                    Konfirmasi Admin
                </a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link position-relative" id="diproses-tab" data-toggle="tab" href="#diproses"
                    role="tab" aria-controls="diproses" aria-selected="false">
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light"
                        style="top: -10px; right: -17px;"
                        id="place_count_diproses">{{ $count_sale }}</span>
                    Diproses
                </a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link position-relative" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab"
                    aria-controls="selesai" aria-selected="false">
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light"
                        style="top: -10px; right: -17px;" id="place_count_selesai">{{ $count_done }}</span>
                    Selesai
                </a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link position-relative" id="cancel-tab" data-toggle="tab" href="#cancel" role="tab"
                    aria-controls="cancel" aria-selected="false">
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light"
                        style="top: -10px; right: -17px;" id="place_count_cancel">{{ $count_cancel }}</span>
                    Transaksi Cancel
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent2">
            <div class="tab-pane fade show active" id="semua" role="tabpanel" aria-labelledby="semua-tab">
                <div class="card">
                    <div class="card-header">
                        <h4>Semua Transaksi</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-success" onclick="sync_status()"><i class="fa fa-sync mr-1"></i> Sync Status</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tb_semua" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">No Invoice</th>
                                        <th scope="col" class="text-center">Tanggal Transaksi</th>
                                        <th scope="col" class="text-center">Customer</th>
                                        <th scope="col" class="text-center">Sales</th>
                                        <th scope="col" class="text-center">Pengiriman</th>
                                        <th scope="col" class="text-center">Biaya Pengiriman</th>
                                        <th scope="col" class="text-center">Total Harga</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center" style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="konfirmasiAdmin" role="tabpanel" aria-labelledby="konfirmasiAdmin-tab">
                <div class="card">
                    <div class="card-header">
                        <h4>Konfirmasi Admin</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-success" onclick="sync_status()"><i class="fa fa-sync mr-1"></i> Sync Status</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tb_konfirmasi_admin" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">No Invoice</th>
                                        <th scope="col" class="text-center">Tanggal Transaksi</th>
                                        <th scope="col" class="text-center">Customer</th>
                                        <th scope="col" class="text-center">Sales</th>
                                        <th scope="col" class="text-center">Pengiriman</th>
                                        <th scope="col" class="text-center">Biaya Pengiriman</th>
                                        <th scope="col" class="text-center">Total Harga</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center" style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="diproses" role="tabpanel" aria-labelledby="diproses-tab">
                <div class="card">
                    <div class="card-header">
                        <h4>Diproses</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-success" onclick="sync_status()"><i class="fa fa-sync mr-1"></i> Sync Status</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tb_diproses" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">No Invoice</th>
                                        <th scope="col" class="text-center">Tanggal Transaksi</th>
                                        <th scope="col" class="text-center">Customer</th>
                                        <th scope="col" class="text-center">Sales</th>
                                        <th scope="col" class="text-center">Pengiriman</th>
                                        <th scope="col" class="text-center">Biaya Pengiriman</th>
                                        <th scope="col" class="text-center">Total Harga</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center" style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
                <div class="card">
                    <div class="card-header">
                        <h4>Selesai</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-success" onclick="sync_status()"><i class="fa fa-sync mr-1"></i> Sync Status</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tb_selesai" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">No Invoice</th>
                                        <th scope="col" class="text-center">Tanggal Transaksi</th>
                                        <th scope="col" class="text-center">Customer</th>
                                        <th scope="col" class="text-center">Sales</th>
                                        <th scope="col" class="text-center">Pengiriman</th>
                                        <th scope="col" class="text-center">Biaya Pengiriman</th>
                                        <th scope="col" class="text-center">Total Harga</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center" style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="cancel" role="tabpanel" aria-labelledby="cancel-tab">
                <div class="card">
                    <div class="card-header">
                        <h4>Transaksi Cancel</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-success" onclick="sync_status()"><i class="fa fa-sync mr-1"></i> Sync Status</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tb_cancel" width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col" class="text-center">No Invoice</th>
                                        <th scope="col" class="text-center">Tanggal Transaksi</th>
                                        <th scope="col" class="text-center">Customer</th>
                                        <th scope="col" class="text-center">Sales</th>
                                        <th scope="col" class="text-center">Pengiriman</th>
                                        <th scope="col" class="text-center">Biaya Pengiriman</th>
                                        <th scope="col" class="text-center">Total Harga</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center" style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    var tb_semua = $('#tb_semua').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/transaksi/datatables',
            type: 'GET',
            data: {
                'status': 'semua'
            },
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,5,6,7,8] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'no_invoice' },
            { data: 'created_at_desc' },
            { data: 'user.nama' },
            { data: 'sales.nama' },
            { data: 'jasa_pengiriman' },
            { data: 'ongkos_kirim',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'total_harga',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'status' },
            { data: 'total_harga' },
        ],
        rowCallback : function(row, data){
            $('td:eq(1)', row).html(`<a href="/admin/transaksi/detail/${data.uuid}" target="_blank">${data.no_invoice}</a>`);

            $('td:eq(8)', row).html(`<span class="badge badge-${data.color}">${data.status_desc}</span>`);

            var url_invoice   = "/admin/transaksi/detail/" + data.uuid;

            if(data.status_desc == 'Konfirmasi Admin'){
                $('td:eq(9)', row).html(`
                    <a href="${url_invoice}" target="_blank"><button class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button></a>
                `);
            }else if(data.status_desc == 'Diproses'){
                $('td:eq(9)', row).html(`
                    <button class="btn btn-warning btn-sm m-2" onclick="cetak_resi_pengiriman(${data.id})"><i class="fas fa-file-pdf mr-1"></i></i>Cetak Resi</button>
                `);
            }else{
                $('td:eq(9)', row).html(`
                    <div style="border:2px dashed black;padding: 5px;text-align: center;">
                        No Action
                    </div>
                `);
            }

        }
    });

    var tb_konfirmasi_admin = $('#tb_konfirmasi_admin').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/transaksi/datatables',
            type: 'GET',
            data: {
                'status': 'konfirmasi_admin'
            },
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,5,6,7,8] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'no_invoice' },
            { data: 'created_at_desc' },
            { data: 'user.nama' },
            { data: 'sales.nama' },
            { data: 'jasa_pengiriman' },
            { data: 'ongkos_kirim',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'total_harga',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'status' },
            { data: 'total_harga' },
        ],
        rowCallback : function(row, data){
            $('td:eq(1)', row).html(`<a href="/admin/transaksi/detail/${data.uuid}" target="_blank">${data.no_invoice}</a>`);

            $('td:eq(8)', row).html(`<span class="badge badge-${data.color}">${data.status_desc}</span>`);

            var url_invoice   = "/admin/transaksi/detail/" + data.uuid;

            if(data.status_desc == 'Konfirmasi Admin'){
                $('td:eq(9)', row).html(`
                    <a href="${url_invoice}" target="_blank"><button class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button></a>
                `);
            }else{
                $('td:eq(9)', row).html(`
                    <div style="border:2px dashed black;padding: 5px;text-align: center;">
                        No Action
                    </div>
                `);
            }
        }
    });

    var tb_diproses = $('#tb_diproses').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/transaksi/datatables',
            type: 'GET',
            data: {
                'status': 'diproses'
            },
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,5,6,7,8] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'no_invoice' },
            { data: 'created_at_desc' },
            { data: 'user.nama' },
            { data: 'sales.nama' },
            { data: 'jasa_pengiriman' },
            { data: 'ongkos_kirim',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'total_harga',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'status' },
            { data: 'total_harga' },
        ],
        rowCallback : function(row, data){
            $('td:eq(1)', row).html(`<a href="/admin/transaksi/detail/${data.uuid}" target="_blank">${data.no_invoice}</a>`);

            $('td:eq(8)', row).html(`<span class="badge badge-${data.color}">${data.status_desc}</span>`);

            var url_invoice   = "/admin/transaksi/detail/" + data.uuid;

            if(data.status_desc == 'Diproses'){
                $('td:eq(9)', row).html(`
                    <button class="btn btn-warning btn-sm m-2" onclick="cetak_resi_pengiriman(${data.id})"><i class="fas fa-file-pdf mr-1"></i></i>Cetak Resi</button>
                `);
            }else{
                $('td:eq(9)', row).html(`
                    <div style="border:2px dashed black;padding: 5px;text-align: center;">
                        No Action
                    </div>
                `);
            }
        }
    });

    var tb_selesai = $('#tb_selesai').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/transaksi/datatables',
            type: 'GET',
            data: {
                'status': 'selesai'
            },
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,5,6,7,8] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'no_invoice' },
            { data: 'created_at_desc' },
            { data: 'user.nama' },
            { data: 'sales.nama' },
            { data: 'jasa_pengiriman' },
            { data: 'ongkos_kirim',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'total_harga',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'status' },
            { data: 'total_harga' },
        ],
        rowCallback : function(row, data){
            $('td:eq(1)', row).html(`<a href="/admin/transaksi/detail/${data.uuid}" target="_blank">${data.no_invoice}</a>`);

            $('td:eq(8)', row).html(`<span class="badge badge-${data.color}">${data.status_desc}</span>`);

            $('td:eq(9)', row).html(`
                <div style="border:2px dashed black;padding: 5px;text-align: center;">
                    No Action
                </div>
            `);
        }
    });

    var tb_cancel = $('#tb_cancel').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/transaksi/datatables',
            type: 'GET',
            data: {
                'status': 'cancel'
            },
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,5,6,7,8] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'no_invoice' },
            { data: 'created_at_desc' },
            { data: 'user.nama' },
            { data: 'sales.nama' },
            { data: 'jasa_pengiriman' },
            { data: 'ongkos_kirim',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'total_harga',  render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) },
            { data: 'status' },
            { data: 'total_harga' },
        ],
        rowCallback : function(row, data){
            $('td:eq(1)', row).html(`<a href="/admin/transaksi/detail/${data.uuid}" target="_blank">${data.no_invoice}</a>`);

            $('td:eq(8)', row).html(`<span class="badge badge-${data.color}">${data.status_desc}</span>`);

            $('td:eq(9)', row).html(`
                <div style="border:2px dashed black;padding: 5px;text-align: center;">
                    No Action
                </div>
            `);
        }
    });

    function cetak_resi_pengiriman(id){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/admin/transaksi/cetak-resi-pengiriman/" + id,
            type: "GET",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.status == 200){
                    console.log("TIPE PDF")
                    var data = b64toBlob(response.data.base64);

                    //UNTUK OPEN NEW TAB
                    var file = new Blob([data], {type: 'application/pdf'});
                    var fileURL = URL.createObjectURL(file);
                    window.open(fileURL);
                }else if(response.status == 300){
                    swal(response.message, { icon: 'error', });
                }
            },
            error: function(blob){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                swal("Oops! Terjadi kesalahan segera hubungi tim IT", {  icon: 'error', });
            }
        });
    }

    function sync_status(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/api/odoo/sync-status",
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.status === 200){
                    swal(response.message, {  icon: 'success', }).then(function(){
                        location.reload();
                    });;
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
