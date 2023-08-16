@extends('partial.app')
@section('title','Master User')

@section('css')
<style>
    .selectgroup-input:checked + .selectgroup-button{
        background: #21b361;
    }
    
    .selectgroup-input:checked + .selectgroup-button.selectgroup-primary{
        background: #0a9447;
    }

    .checkbox-group {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data User</h4>
               <div class="card-header-action">
                  <button type="button" class="btn btn-warning" onclick="add();"><i class="fa fa-plus mr-1"></i> Tambah User</button>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                <table class="table table-striped" id="tb" width="100%">
                    <thead>
                       <tr>
                          <th scope="col" class="text-center">No</th>
                          <th scope="col" class="text-center">Nama</th>
                          <th scope="col" class="text-center">Email</th>
                          <th scope="col" class="text-center">Telepon</th>
                          <th scope="col" class="text-center">Access Menu</th>
                          <th scope="col" class="text-center" style="width: 25%">Actions</th>
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
          <form id="form_submit_request" action="/admin/user/store-update" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Nama</label>
                              <input type="text" hidden class="form-control" name="type" id="type">
                              <input type="text" hidden class="form-control" name="id" id="id">
                              <input class="form-control" type="text" id="nama" name="nama" >
                              <span class="d-flex text-danger invalid-feedback" id="invalid-nama-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" type="text" id="email" name="email" >
                            <span class="d-flex text-danger invalid-feedback" id="invalid-email-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12 access-menu">
                        <div class="form-group">
                            <div class="radio-group">
                                <label>Access Menu</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="access_group" value="all access" class="selectgroup-input radio-group" checked="">
                                        <span class="selectgroup-button selectgroup-button-icon selectgroup-primary"><i class="fas fa-user-cog mr-1"></i> All Access</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="access_group" value="select access" class="selectgroup-input radio-group">
                                        <span class="selectgroup-button selectgroup-button-icon selectgroup-primary"><i class="fas fa-user-lock mr-1"></i> Select Access</span>
                                    </label>
                                </div>
                            </div>
                            <div class="checkbox-group mt-2" style="flex-direction: column; gap: 5px; background: #3232320d; padding: 15px; border-radius: 15px">
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="1" class="selectgroup-input">
                                        <span class="selectgroup-button">Master Sales</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="2" class="selectgroup-input">
                                        <span class="selectgroup-button">Master Pelanggan</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="3" class="selectgroup-input">
                                        <span class="selectgroup-button">Master Produk</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="4" class="selectgroup-input">
                                        <span class="selectgroup-button">Master Administrator</span>
                                    </label>
                                </div>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="5" class="selectgroup-input">
                                        <span class="selectgroup-button">Edit Absensi Sales</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="6" class="selectgroup-input">
                                        <span class="selectgroup-button">Edit Kunjungan Sales</span>
                                    </label>
                                </div>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="7" class="selectgroup-input">
                                        <span class="selectgroup-button">Absensi Sales</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="8" class="selectgroup-input">
                                        <span class="selectgroup-button">Kunjungan Sales</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="9" class="selectgroup-input">
                                        <span class="selectgroup-button">Log Sales</span>
                                    </label>
                                </div>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="10" class="selectgroup-input">
                                        <span class="selectgroup-button">Transaksi</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="11" class="selectgroup-input">
                                        <span class="selectgroup-button">Jasa Pengiriman</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="access_menu[]" value="12" class="selectgroup-input">
                                        <span class="selectgroup-button">Config</span>
                                    </label>
                                </div>
                            </div>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-access_menu-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Telepon</label>
                            <input class="form-control" type="text" id="telepon" name="telepon" >
                            <span class="d-flex text-danger invalid-feedback" id="invalid-telepon-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" type="text" style="height:100px" id="alamat" name="alamat" ></textarea>
                            <span class="d-flex text-danger invalid-feedback" id="invalid-alamat-feedback"></span>
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
        serverSide: true,
        ajax: {
            url: '/admin/user/datatables',
            type: 'GET'
        },
        columnDefs: [
            { className: 'text-center', targets: [0,1,2,3,4,5] },
            { targets: 4, width: '455px' }, // Set width for the second column (index 1
            { targets: 5, width: '85px' }
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'nama' },
            { data: 'email' },
            { data: 'telepon' },
            { data: 'alamat', orderable: false },
            { data: 'access_menu[]', orderable: false },
        ],
        rowCallback : function(row, data){
            var url_edit   = "/admin/user/detail/" + data.id;
            var url_delete = "/admin/user/delete/" + data.id;
            $('td:eq(5)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="edit('${url_edit}')"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="delete_action('${url_delete}','${data.nama}')"><i class="fa fa-trash"> </i></button>
            `);
                
            let element = '';

            if(data.access_menu == 0){
               element += `<span class="badge badge-primary mr-2">All Access</span>` 
            } else {
                let text_access_menu = [];
                data.access_menu.forEach(element => {
                    if(element == 1){text_access_menu.push('Master Sales')};                
                    if(element == 2){text_access_menu.push('Master Pelanggan')};                
                    if(element == 3){text_access_menu.push('Master Produk')};                
                    if(element == 4){text_access_menu.push('Master Administrator')};                
                    if(element == 5){text_access_menu.push('Edit Absensi Sales')};                
                    if(element == 6){text_access_menu.push('Edit Kunjungan Sales')};                
                    if(element == 7){text_access_menu.push('Absensi Sales')};                
                    if(element == 8){text_access_menu.push('Kunjungan Sales')};                
                    if(element == 9){text_access_menu.push('Log Sales')};                
                    if(element == 10){text_access_menu.push('Transaksi')};                
                    if(element == 11){text_access_menu.push('Jasa Pengiriman')};                
                    if(element == 12){text_access_menu.push('Config')};                
                });
    
                text_access_menu.forEach(data => {
                    element += `<span class="badge badge-success mr-2">${data}</span>`;
                });
            }

            $('td:eq(4)', row).html(`<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px">` + element + `</div>`);
        }
    });

    function add(){
        $("#modal").modal('show');
        $(".modal-title").text('Tambah User');
        $("#form_submit_request")[0].reset();
        $('.password-label').text('Change Passsword');
        $('.checkbox-group').css("display", "none");
        $('.access-menu').after(`<div class="col-12 col-md-12 col-lg-12 password-input">
            <div class="form-group">
                <label>Password</label>
                <input class="form-control" type="password" id="password" name="password" >
                <span class="d-flex text-danger invalid-feedback" id="invalid-password-feedback"></span></div>
            </div>`
        );
        reset_all_select();
    }

    function custom_edit_action(url, modal_text){
        $('input,textarea,select.is-invalid').removeClass('is-invalid');
        $('.password-input').remove();
        $("#form_submit_request")[0].reset();
        $('.checkbox-group').css("display", "none");
        $('.invalid-feedback').text('');
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
                if(response['access_menu'] !== 0){
                    $('[name=access_group]').prop("checked", true).trigger('change');
                    $.each(response['access_menu'], function(index, value) {
                        $('input[type="checkbox"][value="' + value + '"]').prop('checked', true);;
                    });
                }
                Object.keys(response).forEach(function (key) {
                    var elem_name = $('[name=' + key + ']');
                    elem_name.val(response[key]);
                });
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
        });
    }
    
    function edit(url){
        custom_edit_action(url, 'Edit User');
        $("#type").val('update');
    }

    $('.radio-group').change(function(){
        let value = $("input[name='access_group']:checked").val();
        if(value == 'select access'){
            $('.checkbox-group').css("display", "flex");
        } else {
            $('.checkbox-group').css("display", "none");
            $('#invalid-access_menu-feedback').text('');
        }
    });
</script>
@endsection
