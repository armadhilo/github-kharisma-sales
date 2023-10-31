@extends('partial.app')
@section('title','Cek Harga Produk')

@section('content')
    <div class="section-body">
        <div class="col-6">
            <div class="form-group">
                <label>SKU</label>
                  <input class="form-control" type="text" id="sku" name="sku">
                  <small>SKU harus dipisahkan dengan sparator "|"</small>
                  <br>
                  <small>contoh -> 5010073|5029183</small>
                  <br>
                  <button class="btn btn-primary" onclick="cek_harga_produk()">Cek Produk</button>
            </div>
        </div>
        <div class="col-6" id="place_data">
        </div>
    </div>
@endsection

@section('js')
    <script>
        function cek_harga_produk(){
            $('#place_data').html('');
            $("#modal_loading").modal('show');
            $.ajax({
                url : "/api/ecom/product/check-harga-produk/" + $('#sku').val(),
                type: "GET",
                success: function(response){
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    if(response.code == 200){
                        response.data.forEach(element1 => {

                            $('#place_data').append(`
                                <div class="card">
                                    <div class="card-header">
                                        <b>${element1.product_name}</b>
                                    </div>
                                    <div class="card-body">
                                        <dl id="place_data_${element1.sku}">
                                        </dl>
                                    </div>
                                </div>
                            `);

                            element1.product_uom_original.forEach(element2 => {

                                $('#place_data_' + element1.sku).append(`
                                    <dt>${element2.type} - ${element2.label}</dt>
                                `);

                                if (element2.product_pricelist.length != 0) {
                                    element2.product_pricelist.forEach(element3 => {

                                        $('#place_data_' + element1.sku).append(`
                                            <dd>    - Rp. ${fungsiRupiah(parseInt(element3.price))} | min quantity : ${element3.min_quantity}</dd>
                                        `);
                                    });
                                }else{
                                    $('#place_data_' + element1.sku).append(`
                                        <dd>kosong</dd>
                                    `);
                                }

                            });
                        });
                    }
                },error: function (jqXHR, textStatus, errorThrown){
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    Swal.fire('Oops!','Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')','error');
                }
            });
        }
    </script>
@endsection

