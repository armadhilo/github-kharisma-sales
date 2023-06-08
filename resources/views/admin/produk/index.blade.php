@extends('partial.app')
@section('title','Produk')

@section('content')

<div class="section-body">
   <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
         <div class="card">
            <div class="card-header">
               <h4>Data Produk</h4>
               <div class="card-header-action">
                  <button type="button" class="btn btn-success" onclick="show_modal_sync()"><i class="fa fa-sync mr-1"></i> Sync Produk</button>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                <table class="table table-striped" id="tb" width="100%">
                    <thead>
                       <tr>
                          <th scope="col" class="text-center">No</th>
                          <th scope="col" class="text-center">Gambar</th>
                          <th scope="col" class="text-center">Nama</th>
                          <th scope="col" class="text-center">SKU</th>
                          <th scope="col" class="text-center">Stock</th>
                          <th scope="col" class="text-center">Unit</th>
                          <th scope="col" class="text-center">B2B</th>
                          <th scope="col" class="text-center">B2C</th>
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
          <form id="form-sync-product" method="POST" autocomplete="off">
             <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="form-group p-md-5 p-sm-0">
                            <div id="drag-and-drop-zone" class="dm-uploader p-5"
                                style="border: 5px dashed #414141a1">
                                <h3 class="mb-5 mt-5 text-muted text-center">Drag &amp; drop files here</h3>
                                <div class="btn btn-primary btn-block mb-5">
                                    <span>Open the file Browser</span>
                                    <input type="file" title='Click to add Files' />
                                </div>
                                <div class="media d-none">
                                    <div class="media-body mb-1">
                                        <div class="d-flex" style="gap: 17px">
                                            <p class="media-text"></p>
                                            <p class="media-status">Status: <span class="status"></span></p>
                                        </div>
                                        <div class="progress mb-2 d-none">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                role="progressbar" style="width: 0%" aria-valuenow="0"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <hr class="mt-1 mb-1" />
                                    </div>
                                </div>
                            </div>
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

    $(document).ready(function(){
        var tb = $('#tb').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/api/ecom/product',
                type: 'GET'
            },
            columnDefs: [
                { className: 'text-center', targets: [0,1,3,4,5,6,7] },
            ],
            columns: [
                { data: "id", render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1;}, searchable: false, orderable: false},
                { data: 'gd_image_small_path' },
                { data: 'product_name' },
                { data: 'sku' },
                { data: 'stock' },
                { data: 'unit' },
                { data: 'null', "defaultContent": "" },
                { data: 'null', "defaultContent": "" },
            ],
            rowCallback : function(row, data){

                for(i=0; i<data.pricelist.length; i++){
                    if(data.pricelist[i].type == "b2b"){
                        $('td:eq(6)', row).html(`Rp. ` + fungsiRupiah(data.pricelist[i].price));
                    } else {
                        $('td:eq(7)', row).html(`Rp. ` + fungsiRupiah(data.pricelist[i].price));
                    }
                }

                if(typeof(data.gd_image_small_path) != "undefined" && data.gd_image_small_path !== null) {
                    $('td:eq(1)', row).html(`
                        <img class="mr-3 rounded" src="data:image/png;base64, ${data.gd_image_small_path}" alt="product">
                    `);
                } else {
                    $('td:eq(1)', row).html(`
                        <img class="mr-3 rounded" style="width: 80px" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png" alt="product">
                    `);
                }
            }
        });

        $('#drag-and-drop-zone').dmUploader({
            url: '/api/odoo/sync-product',
            multiple: false,
            auto: false,
            extFilter: ["txt"],
            dataType: 'json',
            method: 'POST',
            onUploadProgress: function (id, percent, file) {
                ui_multi_update_file_progress(percent);
                $('#modal_loading').modal('show');
            },
            onUploadComplete: function () {
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                swal('Sync Product sedang berjalan', { icon: 'success', });
                $('.media-body .d-flex p.media-status span.status').text('Sync Product sedang berjalan');
                $('.media-body .d-flex p.media-status span.status').addClass('text-success');
            },
            onFileExtError: function(file){
                swal('Pastikan Extensi File .txt!', {
                    icon: 'error',
                });
                $("#drag-and-drop-zone").dmUploader("reset");
            },
            onNewFile: function(id, file){
                // When a new file is added using the file selector or the DnD area
                $('.media').removeClass('d-none');
                $('.progress').addClass('d-none');
                $('p.media-text').text('File: ' + file.name);
                $('.media-body .d-flex p.media-status span.status').text('waiting');
            },
            onBeforeUpload: function (id) {
                // about tho start uploading a file
                ui_multi_update_file_progress(0, '', true);

                $('.media-body .d-flex p.media-status span.status').text('Uploading..')
                $('.progress').removeClass('d-none')
            },
            onUploadError: function (id, xhr, status, errorThrown) {
                swal('xhr: ' + JSON.stringify(xhr) + '\n status: ' + status + '\n errorThrown: ' +
                    errorThrown, {
                        icon: 'error',
                    });
            }
        });

        $('#form-sync-product').submit(function(e){
            e.preventDefault();
            $('#drag-and-drop-zone').dmUploader('start');
        })


        function ui_multi_update_file_progress(percent, color, active) {
            color = (typeof color === 'undefined' ? false : color);
            active = (typeof active === 'undefined' ? true : active);

            var bar = $('.media').find('div.progress-bar');

            bar.width(percent + '%').attr('aria-valuenow', percent);
            bar.toggleClass('progress-bar-striped progress-bar-animated', active);

            if (percent === 0) {
                bar.html('');
            } else {
                bar.html(percent + '%');
            }

            if (color !== false) {
                bar.removeClass('bg-success bg-info bg-warning bg-danger');
                bar.addClass('bg-' + color);
            }
        }

    })

    function show_modal_sync(){
        $("#modal").modal('show');
        $(".modal-title").text('Sync Produk');
        $("#drag-and-drop-zone").dmUploader("reset");
        $('p.media-text').text('');
        $('.media-body .d-flex p.media-status span.status').text('');
        $('.media-body .d-flex p.media-status span.status').removeClass('text-success');
        $('.media').addClass('d-none');
        $('.progress').addClass('d-none');
    }

    // function sync_produk(){
    //     $("#modal_loading").modal('show');
    //     $.ajax({
    //         url : "/api/odoo/sync-product",
    //         type: "GET",
    //         dataType: "JSON",
    //         success: function(response){
    //             setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
    //             if(response.status === 200){
    //                 swal(response.message, {  icon: 'success', });
    //             }else{
    //                 swal(response.message, {  icon: 'error', });
    //             }
    //         },error: function (jqXHR, textStatus, errorThrown){
    //             setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
    //             swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
    //         }
    //     });
    // }
</script>
@endsection
