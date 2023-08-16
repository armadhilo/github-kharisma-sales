@extends('partial.app')
@section('title','Config')
@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Config</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                <table class="table table-striped" id="tb" width="100%">
                    <thead>
                       <tr>
                          <th scope="col" class="text-center">No</th>
                          <th scope="col" class="text-center">Config</th>
                          <th scope="col" class="text-center">Value</th>
                          <th scope="col" class="text-center">Keterangan</th>
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
          <form id="form_submit_request" action="/admin/config/store-update" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                           <label>Config</label>
                           <input type="text" name="config" id="config" class="form-control" disabled>
                        </div>
                     </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Value</label>
                              <input type="text" hidden class="form-control" name="id" id="id">
                              <input class="form-control" type="text" id="value" name="value">
                              <span class="d-flex text-danger invalid-feedback" id="invalid-value-feedback"></span>
                        </div>
                    </div>
                     <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group">
                           <label>Keterangan</label>
                           <input type="text" name="keterangan" id="keterangan" class="form-control" disabled>
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
            url: '/admin/config/datatables',
            type: 'GET'
        },
        columnDefs: [
            { className: 'text-center', targets: [0,2,3,4] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'config' },
            { data: 'value' },
            { data: 'keterangan' },
            { data: 'keterangan' },
        ],
        rowCallback : function(row, data){
            var url_edit   = "/admin/config/detail/" + data.id;
            $('td:eq(4)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="edit('${url_edit}')"><i class="fa fa-edit"></i></button>
            `);
        }
    });

    function edit(url){
        $('form').trigger('reset');
        edit_action(url, 'Edit Config');
        $("#type").val('update');
    }
</script>
@endsection
