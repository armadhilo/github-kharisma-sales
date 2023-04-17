@extends('partial.app')
@section('title','Jasa Pengiriman')

@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Jasa Pengiriman</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                <table class="table table-striped" id="tb" width="100%">
                    <thead>
                       <tr>
                          <th scope="col" class="text-center">No</th>
                          <th scope="col" class="text-center">Jasa Pengiriman</th>
                          <th scope="col" class="text-center">Status</th>
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

@section('js')
<script>

    var tb = $('#tb').DataTable({
        processing: true,
        serverSide: true,
        'paging': false,
        ajax: {
            url: '/admin/jasa-pengiriman/datatables',
            type: 'GET'
        },
        columnDefs: [
            { className: 'text-center', targets: [0,2,3] },
        ],
        columns: [
            { data: 'DT_RowIndex',searchable: false, orderable: false},
            { data: 'nama' },
            { data: 'status_desc' },
            { data: 'status_desc' },
        ],
        rowCallback : function(row, data){

            if(data.status_desc == 'aktif'){
                $('td:eq(2)', row).html(`<span class="badge badge-success">aktif</span>`);
                $('td:eq(3)', row).html(`<button class="btn btn-danger btn-sm" onclick="action('${data.id}','0')"><i class="fa fa-times mr-2"></i>deactivated</button>`);
            }else{
                $('td:eq(2)', row).html(`<span class="badge badge-danger">non-aktif</span>`);
                $('td:eq(3)', row).html(`<button class="btn btn-info btn-sm mr-1" onclick="action('${data.id}','1')"><i class="fa fa-check mr-2"></i>activated</button>`);
            }
        }
    });

    function action(id, status){
        $("#modal_loading").modal('show');
        $.ajax({
            url: '/admin/jasa-pengiriman/action/' + id + "/" + status,
            type: "GET",
            dataType: 'JSON',
            success: function( response, textStatus, jQxhr ){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.status == 200){
                    iziToast.success({
                        title: 'Success!',
                        message: response.message,
                        position: 'topRight'
                        });
                    tb.ajax.reload(null, false);
                }else{
                    iziToast.error({
                        title: 'Error!',
                        message: response.message,
                        position: 'topRight'
                    });
                }
            },
            error: function( jqXhr, textStatus, errorThrown ){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                console.log( errorThrown );
                console.warn(jqXhr.responseText);
            },
        });
    }
</script>
@endsection
