@extends('partial.app')
@section('title','Edit Kunjungan Sales')

@section('content')
<div class="section-body">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4>Data Kunjungan Sales</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tb" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Tanggal</th>
                                <th scope="col" class="text-center">Sales</th>
                                <th scope="col" class="text-center">Pelanggan</th>
                                <th scope="col" class="text-center">Waktu Mulai</th>
                                <th scope="col" class="text-center">Waktu Selesai</th>
                                <th scope="col" class="text-center">Lama Waktu (Menit)</th>
                                <th scope="col" class="text-center" style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                    </table>
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
          <form id="form_submit_request" action="/admin/edit-kunjungan-sales/update" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Sales</label>
                            <input type="hidden" name="id">
                            <input type="text" readonly class="form-control" id="sales">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Pelanggan</label>
                            <input type="text" readonly class="form-control" id="pelanggan">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control required-field" name="date" id="date">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Waktu Mulai</label>
                            <input type="time" class="form-control required-field" name="time_start" id="time_start">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Waktu Selesai</label>
                            <input type="time" class="form-control required-field" name="time_end" id="time_end">
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
@endsection

@section('js')
<script>
    
    var tb = $('#tb').DataTable({
        processing: true,
        serverSide: false,
        bDestroy: true,
        ajax: {
            url: '/admin/edit-kunjungan-sales/datatables',
            type: 'GET',
            error: function (xhr, error, thrown) {
                console.error('AJAX Error:', xhr.status, xhr.responseText);
                alert('Terjadi error saat mengambil data. Cek console.');
            }
        },
        columnDefs: [{
            className: 'text-center',
            targets: [0, 1, 2, 3, 4, 5, 6, 7]
        }],
        columns: [
            { data: 'DT_RowIndex', searchable: false, orderable: true },
            { data: 'date' },
            { data: 'sales_nama', defaultContent: '-' },
            { data: 'pelanggan_nama', defaultContent: '-' },
            { data: 'time_start' },
            { data: 'time_end' },
            { data: 'total_time' },
            { data: 'total_time' },
        ],
        rowCallback: function (row, data) {
            try {
                var url_edit = "/admin/edit-kunjungan-sales/detail/" + data.id;
    
                $('td:eq(7)', row).html(`
                    <button class="btn btn-info btn-sm mr-1"
                        onclick="edit_kunjungan('${url_edit}', '${data.sales_nama}', '${data.pelanggan_nama}')">
                        <i class="fa fa-edit"></i>
                    </button>
                `);
            } catch (e) {
                console.error('Row error on ID:', data?.id, e, data);
            }
        }
    });

    function edit_kunjungan(url, nama_sales, nama_pelanggan){
        edit_action(url, 'Edit Kunjungan Sales');
        $("#form_submit_request")[0].reset();
        reset_all_select();
        $('#sales').val(nama_sales);
        $('#pelanggan').val(nama_pelanggan);
    }
</script>
@endsection
