@extends('partial.app')
@section('title','Absensi Sales')

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
                <h4>Data Absensi Sales</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div style="z-index: 1000; position: relative; max-width: 50%">
                        <label class="text-success" style="font-weight: bold">Filter</label>
                        <div class="d-flex align-items-center" style="margin: 0px 0 15px 0; gap: 10px">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker required-field" name="tanggal_mulai" id="tanggal_mulai">
                            </div>
                            <div>
                                <p>S/D</p>
                            </div>
                            <div class="form-group">
                                {{-- <label>Tanggal Akhir</label> --}}
                                <input type="text" class="form-control datepicker" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-success ml-2" onclick="filter_data()">Filter Data</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped" id="tb" width="100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Sales</th>
                                <th scope="col" class="text-center">Tanggal Mulai</th>
                                <th scope="col" class="text-center">Waktu Mulai</th>
                                <th scope="col" class="text-center">Tanggal Selesai</th>
                                <th scope="col" class="text-center">Waktu Selesai</th>
                                <th scope="col" class="text-center">Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
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
                url: '/admin/absensi-sales/datatables',
                type: 'GET',
                data: {
                    'tanggal_mulai': $('#tanggal_mulai').val(),
                    'tanggal_selesai': $('#tanggal_selesai').val(),
                },
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
                { data: 'badge_custom' },
            ]
            // rowCallback: function (row, data) {
            //     var url_edit   = "/admin/kunjungan-sales/detail/" + data.id;
            //     $('td:eq(4)', row).html(`
            //         <button class="btn btn-info btn-sm mr-1" onclick="view_kunjungan('${url_edit}')"><i class="fa fa-eye"></i></button>
            //     `);
            // }
        });
    }

    function view_kunjungan(url){
        edit_action(url, 'Detail Kunjungan');
        $("#view_form")[0].reset();
        reset_all_select();
    }
</script>
@endsection
