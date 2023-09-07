@extends('partial.app')
@section('title','Master Sales')
@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Sales</h4>
               <div class="card-header-action">
                  <button type="button" hidden class="btn btn-warning" onclick="add();"><i class="fa fa-plus mr-1"></i> Tambah Sales</button>
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
                          <th scope="col" class="text-center">Alamat</th>
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
          <form id="form_submit_request" action="/admin/sales/store-update" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Nama</label>
                              <input type="text" hidden class="form-control" name="type" id="type">
                              <input type="text" hidden class="form-control" name="id" id="id">
                              <input class="form-control" type="text" id="nama" name="nama">
                              <span class="d-flex text-danger invalid-feedback" id="invalid-nama-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" type="text" id="email" name="email">
                            <span class="d-flex text-danger invalid-feedback" id="invalid-email-feedback"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Password <span class="text-danger">Di isi apabila password sales ingin dirubah</span></label>
                            <input class="form-control" type="password" id="password" name="password" >
                            <span class="d-flex text-danger invalid-feedback" id="invalid-password-feedback"></span>
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
            url: '/admin/sales/datatables',
            type: 'GET'
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
        rowCallback : function(row, data){
            var url_edit   = "/admin/sales/detail/" + data.id;
            var url_delete = "/admin/sales/delete/" + data.id;
            $('td:eq(5)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="edit('${url_edit}')"><i class="fa fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="delete_action('${url_delete}','${data.nama}')"><i class="fa fa-trash"> </i></button>
            `);
        }
    });

    function add(){
        $("#modal").modal('show');
        $(".modal-title").text('Tambah Sales');
        $("#form_submit_request")[0].reset();
        reset_all_select();
    }

    function edit(url){
        edit_action(url, 'Edit Sales');
        $("#type").val('update');
    }
</script>
@endsection
