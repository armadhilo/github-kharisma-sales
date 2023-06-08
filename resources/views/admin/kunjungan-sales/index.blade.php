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
                <h5 class="modal-title">Detail Kunjungan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="view_form">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" readonly class="form-control" name="date" id="date">
                             </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Total Kunjungan</label>
                                <input type="text" readonly class="form-control" name="total_time" id="total_time">
                             </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Waktu Mulai</label>
                                <input type="time" readonly class="form-control" name="time_start" id="time_start">
                             </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Waktu Selesai</label>
                                <input type="time" readonly class="form-control" name="time_end" id="time_end">
                             </div>
                        </div>
                    </div>
                </form>
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
        var d           = new Date();
        var date        = ("0" + d.getDate()).slice(-2);
        var m           = ("0" + (d.getMonth() + 1)).slice(-2);
        var y           = d.getFullYear();
        var date_now    = y + "-" + m + "-" + date;
        $('#tanggal_mulai').datepicker('setDate', date_now);
        $('#tanggal_selesai').datepicker('setDate', date_now);
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
                targets: [0, 1, 4]
            }, ],
            columns: [
                { data: 'DT_RowIndex',searchable: false,orderable: false },
                { data: 'date' },
                { data: 'sales.nama' },
                { data: 'pelanggan.nama' },
                { data: 'date' },
            ],
            rowCallback: function (row, data) {
                var url_edit   = "/admin/kunjungan-sales/detail/" + data.id;
                $('td:eq(4)', row).html(`
                    <button class="btn btn-info btn-sm mr-1" onclick="view_kunjungan('${url_edit}')"><i class="fa fa-eye"></i></button>
                `);
            }
        });
    }

    function view_kunjungan(url){
        edit_action(url, 'Detail Kunjungan');
        $("#view_form")[0].reset();
        reset_all_select();
    }
</script>
@endsection
