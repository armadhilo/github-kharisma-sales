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
                  <button type="button" class="btn btn-success" onclick="sync_produk()"><i class="fa fa-sync mr-1"></i> Sync Produk</button>
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

@section('js')
<script>

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
            { data: 'image_small' },
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

            if(typeof(data.image_small) != "undefined" && data.image_small !== null) {
                $('td:eq(1)', row).html(`
                    <img class="mr-3 rounded" src="data:image/png;base64, ${data.image_small}" alt="product">
                `);
            } else {
                $('td:eq(1)', row).html(``);
            }
        }
    });

    function sync_produk(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/api/odoo/sync-product",
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.status === 200){
                    swal(response.message, {  icon: 'success', });
                }else{
                    swal(response.message, {  icon: 'error', });
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
        });
    }
</script>
@endsection
