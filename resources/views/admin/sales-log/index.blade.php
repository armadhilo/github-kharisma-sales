@extends('partial.app')
@section('title','Log Sales')
@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Sales</h4>
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
                          <th scope="col" class="text-center" style="width: 15%">Actions</th>
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
        ajax: {
            url: '/admin/sales-log/datatables',
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
            var url_edit   = "/admin/sales-log/detail/" + data.id;
            $('td:eq(5)', row).html(`
                <button class="btn btn-info btn-sm mr-1" onclick="window.open('${url_edit}', '_blank');"><i class="fa fa-route"></i></button>
            `);
        }
    });
</script>
@endsection
