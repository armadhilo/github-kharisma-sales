@extends('partial.app')
@section('title','Kunjungan Sales')

@section('css')
<style>
    #tb_wrapper{
        margin-top: -77px;
    }

    #tb_filter{
        font-size: 14px;
    }

    input.datepicker:hover{
        cursor: pointer;
    }

    #tb_filter label input[type=search]{
        height: 39px;
        padding: 10px 15px;
        margin-bottom: 15px;
    }

    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
            max-width:1200px;
        }
    }
</style>
@endsection

@section('content')
<div class="section-body">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4>Data Kunjungan Sales</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table_sales" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Nama Sales</th>
                                <th scope="col" class="text-center">Email</th>
                                <th scope="col" class="text-center">Telepon</th>
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
@endsection

@section('modal')
<div class="modal fade" role="dialog" id="modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
       <div class="modal-content">
            <div class="modal-header br">
                <h5 class="modal-title">Detail Kunjungan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <div style="z-index: 1000; position: relative" style="max-width: 60%">
                        <div class="d-flex align-items-center" style="margin: 0px 0 15px 0; gap: 15px">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="text" class="form-control datepicker-bottom required-field" name="tanggal_mulai" id="tanggal_mulai">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="text" class="form-control datepicker-bottom" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            <div class="form-group">
                                <button type="button" style="margin-top: 20px" class="btn btn-success" id="button_filter_data">Filter Data</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped" id="tb" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Tanggal</th>
                                <th scope="col" class="text-center">Sales</th>
                                <th scope="col" class="text-center">Pelanggan</th>
                                <th scope="col" class="text-center">Waktu Mulai</th>
                                <th scope="col" class="text-center">Waktu Selesai</th>
                                <th scope="col" class="text-center" style="width: 5%">Total Kunjungan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
       </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function(){
        var table_sales = $('#table_sales').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/admin/sales/datatables',
                type: 'GET',
            },
            columnDefs: [
                { className: 'text-center', targets: [0,1,2,3,4,5] },
            ],
            columns: [
                { data: 'DT_RowIndex',searchable: false, orderable: false},
                { data: 'nama' },
                { data: 'email' },
                { data: 'telepon' },
                { data: 'alamat' },
                { data: 'alamat' },
            ],
            rowCallback: function (row, data) {
                $('td:eq(5)', row).html(`
                    <button class="btn btn-info btn-sm mr-1" onclick="view_kunjungan('${data.id}')"><i class="fa fa-eye"></i></button>
                `);
                $('#button_filter_data').attr("onclick", `filter_data(${data.id})`);
            }
        });
    });

    function filter_data(id){
        var tb = $('#tb').DataTable({
            processing: true,
            serverSide: false,
            "bDestroy": true,
            ajax: {
                url: '/admin/kunjungan-sales/datatables',
                type: 'GET',
                data: {
                    'id': id,
                    'tanggal_mulai': $('#tanggal_mulai').val(),
                    'tanggal_selesai': $('#tanggal_selesai').val(),
                },
            },
            columnDefs: [{
                className: 'text-center',
                targets: [0, 1, 4, 5, 6]
            }, ],
            columns: [
                { data: 'DT_RowIndex',searchable: false,orderable: false },
                { data: 'date' },
                { data: 'sales.nama' },
                { data: 'pelanggan.nama' },
                { data: 'time_start' },
                { data: 'time_end' },
                { data: 'total_time' },
            ],
        });

        console.log($('#tanggal_mulai').val())
    }

    function date_now(){
        var d           = new Date();
        var date        = ("0" + d.getDate()).slice(-2);
        var m           = ("0" + (d.getMonth() + 1)).slice(-2);
        var y           = d.getFullYear();
        var date_now    = y + "-" + m + "-" + date;
        $('#tanggal_mulai').datepicker("setDate", date_now);
        $('#tanggal_selesai').datepicker("setDate", date_now);
    }
    
    function view_kunjungan(id){
        $('#modal').modal('show');
        $('#tanggal_mulai').val('');
        $('#tanggal_selesai').val('');
        $.when(filter_data(id)).then(date_now());
    }
    
</script>
@endsection
