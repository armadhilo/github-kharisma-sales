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
                    <div style="z-index: 1000; position: relative">
                        <div class="d-flex align-items-center" style="margin: 0px 0 15px 0; gap: 15px">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="text" class="form-control datepicker required-field" name="tanggal_mulai" id="tanggal_mulai">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="text" class="form-control datepicker" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            <div class="form-group">
                                <button type="button" style="margin-top: 20px" class="btn btn-success" onclick="filter_data()">Filter Data</button>
                                <button type="button" style="margin-top: 20px" class="btn btn-secondary ml-2 d-none" id="reset_filter" onclick="reset_filter()">Reset Filter</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped" id="tb" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Sales</th>
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
                    <table class="table table-striped" id="tb_detail" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Tanggal</th>
                                <th scope="col" class="text-center">Sales</th>
                                <th scope="col" class="text-center">Pelanggan</th>
                                <th scope="col" class="text-center">Waktu Mulai</th>
                                <th scope="col" class="text-center">Waktu Selesai</th>
                                <th scope="col" class="text-center" style="width: 5%">Lama Waktu (Menit)</th>
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
        filter_data();
    });

    function filter_data(){
        var tb = $('#tb').DataTable({
            processing: true,
            serverSide: false,
            "bDestroy": true,
            ajax: {
                url: '/admin/kunjungan-sales/datatables',
                type: 'GET',
                data: {
                    'tanggal_mulai': $('#tanggal_mulai').val(),
                    'tanggal_selesai': $('#tanggal_selesai').val(),
                },
            },
            columnDefs: [{
                className: 'text-center',
                targets: [0, 2]
            }],
            columns: [
                { data: 'DT_RowIndex',searchable: false,orderable: false },
                { data: 'sales.nama' },
                { data: 'date' },
            ],
            rowCallback: function (row, data) {
                $('td:eq(2)', row).html(`
                    <button class="btn btn-info btn-sm mr-1" onclick="view_kunjungan('${data.sales_id}')"><i class="fa fa-eye"></i></button>
                `);
            }
        });

        if($('#tanggal_mulai').val() !== '' && $('#tanggal_selesai').val() !== ''){
            $('#reset_filter').removeClass('d-none');
        }
    }

    function tb_detail(id){
        var tb_detail = $('#tb_detail').DataTable({
            processing: true,
            serverSide: false,
            "bDestroy": true,
            ajax: {
                url: '/admin/kunjungan-sales/datatables/detail',
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
            }],
            columns: [
                { data: 'DT_RowIndex',searchable: false,orderable: false },
                { data: 'date' },
                { data: 'sales.nama' },
                { data: 'pelanggan.nama' },
                { data: 'time_start' },
                { data: 'time_end' },
                { data: 'total_time' },
            ]
        });
    }

    // function date_now(){
    //     var d           = new Date();
    //     var date        = ("0" + d.getDate()).slice(-2);
    //     var m           = ("0" + (d.getMonth() + 1)).slice(-2);
    //     var y           = d.getFullYear();
    //     var date_now    = y + "-" + m + "-" + date;
    //     $('#tanggal_mulai').datepicker('setDate', date_now);
    //     $('#tanggal_selesai').datepicker('setDate', date_now);
    // }

    function view_kunjungan(id){
        $('#modal').modal('show');
        tb_detail(id);
    }

    function reset_filter(){
        $('#tanggal_mulai').val('');
        $('#tanggal_selesai').val('');
        filter_data();
        $('#reset_filter').addClass('d-none');
    }

</script>
@endsection
