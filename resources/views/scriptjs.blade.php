<script type="text/javascript">

   function check_required(form_id = ""){
      var process_required = true;

      var check_field = ".required-field";
      if(form_id != ""){
         check_field = "#" + form_id + " .required-field";
      }else{
         check_field = ".required-field";
      }

      $(check_field).each(function(){
         var value = $(this).val();
         if ($(this).hasClass("selectric")) {
            if(value === null){
               $(".selectric-required-field > .selectric").addClass("red-border");
               process_required = false;
               $(this).bind('change', function(){
                  $(this).parents().eq(1).children('.selectric').removeClass("red-border");
               });
            }
         } else if($(this).hasClass("select2")){
            if(value === null){
               $(this).parents().eq(0).find('.select2-selection--single').addClass("red-border");
               process_required = false;
               $(this).bind('change', function(){
                  $(this).parents().eq(0).find('.select2-selection--single').removeClass("red-border");
               });
            }
         }else{
            if(value == ""){
               $(this).addClass("is-invalid");
               process_required = false;

               $(this).bind('keyup change', function(){
                     $(this).removeClass("is-invalid");
               });
            } else {
                  $(this).removeClass("is-invalid");
            }
         }
      });
      return process_required;
   }

   $('#form_submit').on('submit', function(e){
      e.preventDefault();

      var form_id = $(this).attr("id");
      if(check_required(form_id) === false){
        Swal.fire('','Mohon isi field kosong','warning');
        return;
      }

      Swal.fire({
            title: 'Yakin?',
            text: "Apakah anda yakin akan menyimpan data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Save Changes'
        }).then((result) => {
            if (result.isConfirmed) {
                $("#modal_loading").modal('show');
                $.ajax({
                    url:  $('#form_submit').attr('action'),
                    type: $('#form_submit').attr('method'),
                    data: $('#form_submit').serialize(),
                    success: function(response){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        if(response.status == 200){
                            Swal.fire('Good job!',response.message,'success');
                            $("#modal").modal('hide');
                            $("#form_submit")[0].reset();
                            reset_all_select();
                            tb.ajax.reload(null, false);
                        }
                        else if(response.status == 201){
                            Swal.fire('Good job!',response.message,'success');
                            $("#modal").modal('hide');
                            window.location.href = response.link;
                        }
                        else if(response.status == 203){
                            Swal.fire('Good job!',response.message,'success');
                            $("#modal").modal('hide');
                            tb.ajax.reload(null, false);
                        }
                        else if(response.status == 300){
                             Swal.fire('Oops!',response.message,'error');
                        }
                    },error: function (jqXHR, textStatus, errorThrown){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
                    }
                });
            }
        })
   });

    $('#form_upload').submit(function(e){
        e.preventDefault();

         var form_id = $(this).attr("id");
         if(check_required(form_id) === false){
            Swal.fire('','Mohon isi field kosong','warning');
            return;
         }

        Swal.fire({
            title: 'Yakin?',
            text: "Apakah anda yakin akan menyimpan data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Save Changes'
        }).then((result) => {
            if (result.isConfirmed) {
                $("#modal_loading").modal('show');
                $.ajax({
                    url:  $('#form_upload').attr('action'),
                    type: $('#form_upload').attr('method'),
                    enctype: 'multipart/form-data',
                    data: new FormData($('#form_upload')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                        //  $("#form_upload")[0].reset();
                        $("#path_file_text").text("");
                        if(response.status == 200){
                            $("#modal").modal('hide');
                            Swal.fire('Good job!',response.message,'success');
                            // iziToast.success({
                            //    title: 'Success!',
                            //    message: response.message,
                            //    position: 'topRight'
                            // });
                            tb.ajax.reload(null, false);
                        }
                        else if(response.status == 201){
                            $("#modal").modal('hide');
                            iziToast.success({
                            title: 'Success!',
                            message: response.message,
                            position: 'topRight'
                            });
                            setTimeout(function () { window.location.href = response.link; }, 1500);
                           
                        }
                        else if(response.status == 203){
                            $("#modal").modal('hide');
                            iziToast.success({
                            title: 'Success!',
                            message: response.message,
                            position: 'topRight'
                            });
                            tb.ajax.reload(null, false);
                        } else{
                            // iziToast.error({
                            // title: 'Error!',
                            // message: response.message,
                            // position: 'topRight'
                            // });
                             Swal.fire('Oops!',response.message,'error');
                        }
                    },error: function (jqXHR, textStatus, errorThrown){
                        setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                         Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
                    }
                });
                }
        });
    })

   function get_path_file(){
      var fullPath = $("#file_excel").val();
      var filename = fullPath.replace(/^.*[\\\/]/, '');
      $("#path_file_text").text(filename);
   }

   function edit_action(url, modal_text){
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
         },error: function (jqXHR, textStatus, errorThrown){
            setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
             Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
         }
      });
   }

   function delete_action(url, nama){
    Swal.fire({
            title: 'Yakin?',
            text: 'Apakah anda yakin akan menghapus data ' + nama + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya! Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
               $("#modal_loading").modal('show');
               $.ajax({
                  url : url,
                  type: "DELETE",
                  dataType: "JSON",
                  success: function(response){
                     setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);

                     if(response.status === 200){
                        Swal.fire('Good job!',response.message,'success');
                        $("#modal").modal('hide');
                        tb.ajax.reload(null, false);
                     }else{
                        Swal.fire('Oops!',response.message,'error');
                     }

                  },error: function (jqXHR, textStatus, errorThrown){
                     setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                      Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
                  }
               });
            }
      });
   }

   function reload(){
      tb.ajax.reload(null,false);
   }

   function reset_all_select(){
      $('.select2').each(function(){
         var name = $(this).attr("name");
         $('[name="'+name+'"]').select2().trigger('change');
      });

      $('.selectric').each(function(){
         var name = $(this).attr("name");
         $('[name="'+name+'"]').selectric();
      });
   }

   //return onkeyupRupiah(this.id);
   function onkeyupRupiah(id){
      var rupiah = document.getElementById(id);
            rupiah.addEventListener('keyup', function(e){
            rupiah.value = fungsiRupiah(this.value);
      });
   }

   //return onkeyupUppercase(this.id);
   function onkeyupUppercase(id){
        var uppercase = document.getElementById(id);
        uppercase.addEventListener('keyup', function(e){
            uppercase.value = this.value.toUpperCase();
      });
   }

   //return onKeypressAngka(event,false);
   function onKeypressAngka(e, decimal) {
      var key;
      var keychar;
      if (window.event) {
         key = window.event.keyCode;
      } else if (e) {
         key = e.which;
      } else {
         return true;
      }
      keychar = String.fromCharCode(key);
      if ((key==null) || (key==0) || (key==8) ||  (key==9) || (key==13) || (key==27) ) {
         return true;
      } else if ((("0123456789").indexOf(keychar) > -1)) {
         return true;
      } else if (decimal && (keychar == ".")) {
         return true;
      } else {
         return false;
      }
   }

    function fungsiRupiah(angka){
        var number_string = angka.toString().replace(/[^,\d]/g, '').toString(),
        split   		= number_string.split(','),
        sisa     		= split[0].length % 3,
        rupiah     		= split[0].substr(0, sisa),
        ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if(ribuan){
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

   function convertToAngka(rupiah)
   {
      return parseInt(rupiah.replace(/,.*|[^0-9]/g, ''), 10).toString();
   }

</script>
