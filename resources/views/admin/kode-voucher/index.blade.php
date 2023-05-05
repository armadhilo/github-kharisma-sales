@extends('partial.app')
@section('title','Kode Voucher')

@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Kode Voucher</h4>
               <div class="card-header-action">
                  <button type="button" class="btn btn-warning" onclick="add();"><i class="fa fa-plus mr-1"></i> Tambah Kode Voucher</button>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                <table class="table table-striped" id="tb" width="100%">
                    <thead>
                       <tr>
                          <th scope="col" class="text-center">No</th>
                          <th scope="col" class="text-center">Kode Voucher</th>
                          <th scope="col" class="text-center">Tipe</th>
                          <th scope="col" class="text-center">Spesifik User</th>
                          <th scope="col" class="text-center">Status</th>
                          <th scope="col" class="text-center">Maksimal Penggunaan</th>
                          <th scope="col" class="text-center">Deskripsi</th>
                          <th scope="col" class="text-center" style="width: 20%">Actions</th>
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
    <div class="modal-dialog modal-xs" role="document">
       <div class="modal-content">
          <div class="modal-header br">
             <h5 class="modal-title"></h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
             </button>
          </div>
          <form id="form_submit_request" action="/admin/kode-voucher/store-update" method="POST" autocomplete="off">
            <input type="text" name="id" id="id" hidden>
            <input type="text" name="type" id="type" hidden>
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-7 col-lg-7">
                        <div class="form-group">
                            <label class="form-label">Harus Klaim (?)</label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" id="is_claim" name="is_claim" value="0" class="selectgroup-input" checked="">
                                <span class="selectgroup-button">Tidak</span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" id="is_claim" name="is_claim" value="1" class="selectgroup-input">
                                <span class="selectgroup-button">Iya</span>
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 col-lg-5">
                        <div class="form-group">
                            <label>Maksimal Penggunaan</label>
                            <input type="number" min="1" class="form-control required-field" name="maksimal_penggunaan" id="maksimal_penggunaan" value="1">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Title Voucher</label>
                            <input type="text" class="form-control required-field" name="title" id="title">
                        </div>
                    </div>
                    <br>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" style="height: 80px;"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="form-label">Tipe Voucher</label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" name="tipe" id="tipe" value="persen" class="selectgroup-input" checked="" onchange="change_tipe()">
                                <span class="selectgroup-button">Voucher Persen</span>
                              </label>
                              {{-- <label class="selectgroup-item">
                                <input type="radio" name="tipe" id="tipe" value="barang" class="selectgroup-input" onchange="change_tipe()">
                                <span class="selectgroup-button">Voucher Barang</span>
                              </label> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Kode Voucher</label>
                            <input type="text" class="form-control required-field" name="kode_voucher" id="kode_voucher" onkeyup="return onkeyupUppercase(this.id)">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="text" class="form-control datepicker required-field" name="tanggal_mulai" id="tanggal_mulai">
                         </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="text" class="form-control datepicker required-field" name="tanggal_selesai" id="tanggal_selesai">
                         </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 voucher-persen">
                        <div class="form-group">
                            <label>Minimal Pembelian (Rp)</label>
                            <input type="text" class="form-control" name="persen_minimal_pembelian" id="persen_minimal_pembelian">
                            <span class="d-flex text-danger invalid-feedback" id="invalid-persen_minimal_pembelian-feedback"></span>
                         </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 voucher-persen">
                        <div class="form-group">
                            <label>Presentase Cashback Poin (%)</label>
                            <input type="number" class="form-control" min="0" max="100" name="persen_presentase_potongan" id="persen_presentase_potongan">
                            <span class="d-flex text-danger invalid-feedback" id="invalid-persen_persentase_potongan-feedback"></span>
                         </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12 voucher-persen">
                        <div class="form-group">
                            <label>Maksimal Cashback Poin (Rp)</label>
                            <input type="text" class="form-control" name="persen_maksimal_potongan" id="persen_maksimal_potongan">
                            <span class="d-flex text-danger invalid-feedback" id="invalid-persen_maksimal_potongan-feedback"></span>
                         </div>
                    </div>
                    <div class="col-12 col-md-8 col-lg-8 voucher-barang">
                        <div class="form-group">
                            <label>Produk Beli</label>
                            <select class="form-control select2" name="master_produk_id_beli" id="master_produk_id_beli">
                                <option selected disabled>- Pilih -</option>
                            </select>
                           <span class="d-flex text-danger invalid-feedback" id="invalid-master_produk_id_beli-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4 voucher-barang">
                        <div class="form-group">
                            <label>Minimal Beli</label>
                            <input type="number" class="form-control" name="minimal_produk_beli" id="minimal_produk_beli">
                            <span class="d-flex text-danger invalid-feedback" id="invalid-minimal_produk_beli-feedback"></span>
                         </div>
                    </div>
                    <div class="col-12 col-md-8 col-lg-8 voucher-barang">
                        <div class="form-group">
                            <label>Produk Bonus</label>
                            <select class="form-control select2" name="master_produk_id_bonus" id="master_produk_id_bonus">
                                <option selected disabled>- Pilih -</option>
                            </select>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-master_produk_id_bonus-feedback"></span>
                         </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4 voucher-barang">
                        <div class="form-group">
                            <label>Jumlah Bonus</label>
                            <input type="number" class="form-control" name="jumlah_produk_bonus" id="jumlah_produk_bonus">
                            <span class="d-flex text-danger invalid-feedback" id="invalid-jumlah_produk_bonus-feedback"></span>
                         </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="form-label">Voucher Spesifik User</label>
                            <div class="selectgroup w-100">
                              <label class="selectgroup-item">
                                <input type="radio" id="voucher_spesifikasi_user" name="voucher_spesifikasi_user" value="N" class="selectgroup-input" checked="" onchange="change_spesifikasi_user()">
                                <span class="selectgroup-button">Tidak</span>
                              </label>
                              <label class="selectgroup-item">
                                <input type="radio" id="voucher_spesifikasi_user" name="voucher_spesifikasi_user" value="Y" class="selectgroup-input" onchange="change_spesifikasi_user()">
                                <span class="selectgroup-button">Iya</span>
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12" id="input_user_id" hidden>
                        <div class="form-group">
                            <label>User ID</label>
                            <select class="form-control select2" name="user_id" id="user_id">
                                <option selected disabled>- Pilih -</option>
                            </select>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-user_id-feedback"></span>
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

{{-- <div class="modal fade" role="dialog" id="modal_history" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
       <div class="modal-content">
            <div class="modal-header br">
                <h5 class="modal-title">Log Penggunaan Voucher</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tb_history" width="100%">
                            <thead>
                               <tr>
                                  <th scope="col" class="text-center">No</th>
                                  <th scope="col" class="text-center">Tanggal</th>
                                  <th scope="col" class="text-center">No Invoice</th>
                                  <th scope="col" class="text-center">Tipe Customer</th>
                                  <th scope="col" class="text-center">Nama</th>
                                  <th scope="col" class="text-center">Total Belanja</th>
                               </tr>
                            </thead>
                         </table>
                       </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
       </div>
    </div>
</div> --}}
@endsection

@section('js')
<script>

    $(document).ready(function(){
        // get_data_master_package();
        // get_data_master_produk();
        change_tipe();
        // get_data_user();

        onkeyupRupiah('persen_minimal_pembelian');
        onkeyupRupiah('persen_maksimal_potongan');
    });

    function change_spesifikasi_user(){
        if(document.querySelector('input[name="voucher_spesifikasi_user"]:checked').value == 'N'){
            $('#user_id').val('');
            $('#input_user_id').attr('hidden',true);
        }else{
            $('#input_user_id').attr('hidden',false);
        }
    }

    function change_tipe(){
        if(document.querySelector('input[name="tipe"]:checked').value == 'persen'){
            $('.voucher-persen').attr('hidden', false);
            $('.voucher-barang').attr('hidden', true);
        }else{
            $('.voucher-persen').attr('hidden',true);
            $('.voucher-barang').attr('hidden',false);
        }
    }

    // function get_data_master_package(){
    //     $("#modal_loading").modal('show');
    //     $.ajax({
    //         url : "/commerce/ecom_seller_final/public/master/get-data-all/MasterPackage",
    //         type: "GET",
    //         dataType: "JSON",
    //         success: function(response){
    //             setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
    //             if(response.status === 200){
    //                 var data = response.data;
    //                 for (var i = 0; i < data.length; i++) {
    //                     $("#master_package_id").append(`<option value="${data[i].id}">${data[i].package}</option>`);
    //                 }
    //             }else{
    //                 iziToast.error({
    //                     title: 'Error!',
    //                     message: response.message,
    //                     position: 'topRight'
    //                 });
    //             }
    //         },error: function (jqXHR, textStatus, errorThrown){
    //             setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
    //             swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
    //         }
    //     });
    // }

    function get_data_user(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/commerce/ecom_seller_final/public/master/data-user-customer-all",
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.status === 200){
                    var data = response.data;
                    $("#user_id").empty();
                    for (var i = 0; i < data.length; i++) {
                        $("#user_id").append(`<option value="${data[i].id}">${data[i].nama}</option>`);
                    }
                }else{
                    iziToast.error({
                        title: 'Error!',
                        message: response.message,
                        position: 'topRight'
                    });
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
        });
    }

    // function get_data_master_produk(){
    //     $("#modal_loading").modal('show');
    //     $.ajax({
    //         url : "/commerce/ecom_seller_final/public/master/data-master-produk-active",
    //         type: "GET",
    //         dataType: "JSON",
    //         success: function(response){
    //             setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
    //             if(response.status === 200){
    //                 var data = response.data;
    //                 $("#master_produk_id_beli").empty();
    //                 $("#master_produk_id_bonus").empty();
    //                 for (var i = 0; i < data.length; i++) {
    //                     $("#master_produk_id_beli").append(`<option value="${data[i].id}">${data[i].nama_produk_desc}</option>`);
    //                     $("#master_produk_id_bonus").append(`<option value="${data[i].id}">${data[i].nama_produk_desc}</option>`);
    //                 }
    //             }else{
    //                 iziToast.error({
    //                     title: 'Error!',
    //                     message: response.message,
    //                     position: 'topRight'
    //                 });
    //             }
    //         },error: function (jqXHR, textStatus, errorThrown){
    //             setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
    //             swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
    //         }
    //     });
    // }

    var tb = $('#tb').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/kode-voucher/datatables',
            type: 'GET'
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,3,4,5,6,7] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'kode_voucher' },
            { data: 'tipe' },
            { data: 'user_desc' },
            { data: 'tipe' },
            { data: 'maksimal_penggunaan' },
            { data: 'deskripsi_bonus' },
            { data: 'tipe' },
        ],
        rowCallback : function(row, data){

            if(data.tipe == 'persen'){
                $('td:eq(2)', row).html(`<span class="badge badge-info">persen</span>`);
            }else{
                $('td:eq(2)', row).html(`<span class="badge badge-primary">barang</span>`);
            }

            $('td:eq(4)', row).html(`<span class="badge badge-success">active</span>`);

            var url_edit   = "/admin/kode-voucher/detail/" + data.id;
            var url_delete = "/admin/kode-voucher/delete/" + data.id;
            $('td:eq(7)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="edit('${url_edit}')"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="delete_action('${url_delete}','${data.kode_voucher}')"><i class="fa fa-trash"> </i></button>
                `);
                // <button class="btn btn-warning btn-sm ml-1" onclick="click_history('${data.kode_voucher}')"><i class="fas fa-history"></i></button>
        }
    });

    // function click_history(kode_voucher){
    //     $('#modal_history').modal('show');
    //     var tb_history = $('#tb_history').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         destroy: true,
    //         ajax: {
    //             url: '/commerce/ecom_seller_final/public/admin/apps/kode-voucher/datatables-history',
    //             type: 'GET',
    //             data: {
    //                 'kode_voucher': kode_voucher,
    //             }
    //         },
    //         columnDefs: [
    //             { className: 'text-center', targets: [0,1,2,3,5] },
    //         ],
    //         columns: [
    //             { data: 'DT_RowIndex',searchable: false, orderable: false},
    //             { data: 'created_at_desc' },
    //             { data: 'no_invoice' },
    //             { data: 'user.tipe_customer.customer' },
    //             { data: 'user.nama' },
    //             { data: 'total_harga_belanja', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
    //         ],
    //         rowCallback : function(row, data){
    //             $('td:eq(2)', row).html(`<a href="/commerce/ecom_seller_final/public/admin/apps/transaksi/${data.uuid}" target="_blank">${data.no_invoice}</a>`);
    //         }
    //     });
    // }

    function add(){
        $("#modal").modal('show');
        $(".modal-title").text('Tambah Kode Voucher');
        $("#form_submit")[0].reset();
        reset_all_select();
    }

    function edit(url){
        edit_action_self(url, 'Edit Kode Voucher');
        $("#type").val('update');
    }

    function edit_action_self(url, modal_text){
      save_method = 'edit';
      $("#modal").modal('show');
      $(".modal-title").text(modal_text);
      $("#modal_loading").modal('show');
      $.ajax({
         url : url,
         type: "GET",
         dataType: "JSON",
         success: function(response){
            setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
            Object.keys(response).forEach(function (key) {
               var elem_name = $('[name=' + key + ']');
               if (elem_name.hasClass('selectric')) {
                  elem_name.val(response[key]).change().selectric('refresh');
               }else if(elem_name.hasClass('select2')){
                  elem_name.select2("trigger", "select", { data: { id: response[key] } });
               }else if(elem_name.hasClass('selectgroup-input')){
                  $("input[name="+key+"][value=" + response[key] + "]").prop('checked', true);
               }else if(elem_name.hasClass('my-ckeditor')){
                  CKEDITOR.instances[key].setData(response[key]);
               }else if(elem_name.hasClass('custom-control-input')){
                  $("input[name="+key+"][value=" + response[key] + "]").prop('checked', true);
               }else if(elem_name.hasClass('time-format')){
                  elem_name.val(response[key].substr(0, 5));
               }else if(elem_name.hasClass('format-rp')){
                  var nominal = response[key].toString();
                  elem_name.val(nominal.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1."));
               }else{
                  elem_name.val(response[key]);
               }
            });

            change_tipe();

            if(response.persen_maksimal_potongan != null){
                $('persen_minimal_pembelian').val((response.persen_minimal_pembelian/1000).toFixed(3));
                $('persen_maksimal_potongan').val((response.persen_maksimal_potongan/1000).toFixed(3));
            }

            if(response.user_id != null){
                $("input[name=voucher_spesifikasi_user][value='Y']").prop("checked", true);
                $('#input_user_id').attr('hidden',false);
            }

         },error: function (jqXHR, textStatus, errorThrown){
            setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
            swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
         }
      });
   }
</script>
@endsection
