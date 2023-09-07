@extends('partial.app')
@section('title','Change Password')

@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Change Password</h4>
            </div>
            <form id="form">
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-12 col-lg-12 col-12">
                        <div class="form-group">
                           <label>Old Password</label>
                           <input type="password" class="form-control" name="old_password" required>
                           </div>
                     </div>
                     <div class="col-md-6 col-lg-6 col-12">
                        <div class="form-group">
                           <label>New Password</label>
                           <input type="password" class="form-control" name="new_password" required>
                           </div>
                     </div>
                     <div class="col-md-6 col-lg-6 col-12">
                        <div class="form-group">
                           <label>Re-type New Password</label>
                           <input type="password" class="form-control" name="retype_password" required>
                           </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer text-right">
                  <button type="submit" class="btn btn-success"><i class="fa fa-save m-1"></i> Change Password</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection


@section('js')
<script>
   $('#form').submit(function(e){
      e.preventDefault();
      swal({
            title: 'Apakah anda yakin?',
            text: 'Apakah anda yakin akan mengubah password anda?',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
      })
      .then((willDelete) => {
            if (willDelete) {
               $("#modal_loading").modal('show');
               $.ajax({
                  url : '/change-password/action-change-password',
                  type: "POST",
                  data: $('#form').serialize(),
                  dataType: "JSON",
                  success: function(response){
                     setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                     if(response.status === 200){
                        swal(response.message, {  icon: 'success', });
                        $("#form")[0].reset();
                     }else{
                        swal(response.message, {  icon: 'error', });
                     }
                  },
                  error: function (jqXHR, textStatus, errorThrown){
                     console.log("Error json " + errorThrown);
                  }
               });
            }
      });
   })

</script>
@endsection
