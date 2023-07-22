@extends('partial.app')
@section('title','Absensi Sales')

@section('content')
<div class="section-body">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4>Data Absensi Sales</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tb" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Sales</th>
                                <th scope="col" class="text-center">Tanggal Mulai</th>
                                <th scope="col" class="text-center">Waktu Mulai</th>
                                <th scope="col" class="text-center">Tanggal Selesai</th>
                                <th scope="col" class="text-center">Waktu Selesai</th>
                                <th scope="col" class="text-center">Actions</th>
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
          <form id="form_submit_request" action="/admin/absensi-sales/update" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Sales</label>
                            <input type="hidden" name="id">
                            <input type="text" readonly class="form-control required-field" id="sales">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" class="form-control required-field" name="date_start" id="date_start">
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
                            <label>Tanggal Selesai</label>
                            <input type="date" class="form-control" name="date_end" id="date_end">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Waktu Selesai</label>
                            <input type="time" class="form-control" name="time_end" id="time_end">
                        </div>
                    </div>
                </div>
             </div>
             <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-success" onclick="reset_absensi();"><i class="fas fa-sync-alt mr-2"></i>Reset Absensi</button>
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
        "bDestroy": true,
        ajax: {
            url: '/admin/absensi-sales/datatables',
            type: 'GET'
        },
        columnDefs: [{
            className: 'text-center',
            targets: [0, 1, 2, 3, 4, 5, 6]
        }, ],
        columns: [
            { data: 'DT_RowIndex',searchable: false,orderable: false },
            { data: 'sales.nama' },
            { data: 'date_start' },
            { data: 'time_start' },
            { data: 'date_end' },
            { data: 'time_end' },
            { data: 'time_end' },
        ],
        rowCallback: function (row, data) {
            var url_edit   = "/admin/absensi-sales/detail/" + data.id;
            $('td:eq(6)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="edit_absensi('${url_edit}', '${data.sales.nama}')"><i class="fa fa-edit"></i></button>
            `);
        }
    });

    function edit_absensi(url, sales_nama){
        edit_action(url, 'Edit Absensi');
        $("#form_submit_request")[0].reset();
        reset_all_select();
        $('#sales').val(sales_nama);
    }

    function reset_absensi(){
        $('#date_end').val(null);
        $('#time_end').val(null);
    }
</script>
@endsection
