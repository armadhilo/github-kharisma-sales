@extends('partial.app')
@section('title','Log Sales')
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
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Sales</h4>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <div style="z-index: 1000; position: relative">
                     <div class="d-flex align-items-center" style="margin: 0px 0 15px 0; gap: 15px">
                           <div class="form-group">
                              <label>Tanggal Mulai</label>
                              <input type="text" class="form-control datepicker required-field" name="tanggal_mulai" id="tanggal_mulai" autocomplete="off">
                           </div>
                           <div class="form-group">
                              <label>Tanggal Akhir</label>
                              <input type="text" class="form-control datepicker" name="tanggal_selesai" id="tanggal_selesai" autocomplete="off">
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

   $(document).ready(function(){
      filter_data();
   });

   function filter_data(){
      var tb = $('#tb').DataTable({
         processing: true,
         serverSide: true,
         "bDestroy": true,
         ajax: {
            url: '/admin/sales-log/datatables',
            type: 'GET',
            data: {
               'tanggal_mulai': $('#tanggal_mulai').val(),
               'tanggal_selesai': $('#tanggal_selesai').val(),
            },
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

      if($('#tanggal_mulai').val() !== '' && $('#tanggal_selesai').val() !== ''){
         $('#reset_filter').removeClass('d-none');
      }
   }


   function reset_filter(){   
      $('#tanggal_mulai').val('');
      $('#tanggal_selesai').val('');
      filter_data();
      $('#reset_filter').addClass('d-none');
   }
</script>
@endsection
