<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>ADMIN PANEL &mdash; KHARISMA</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/public_page/images/favicon.ico')}}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/prism/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/jquery-selectric/selectric.css') }}">
    <link rel="stylesheet" href="{{asset('assets/modules/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
    {{-- <link rel="stylesheet" href="{{asset('assets/modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css')}}"> --}}

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custome.css') }}">
    <link rel="stylesheet" href="{{asset('assets/modules/izitoast/css/iziToast.min.css')}}">

    <style type="text/css" media="print">
        @media print {
            @page { margin: 0; }
        }

        .section-title::before{
            display: none!important;
        }

        .section-title{
            font-size: 16px!important;
            margin: 5px 0 10px 0!important;
        }

        .section .section-title + .section-lead{
            margin: 0!important
        }

        .voucher-title{
            font-size: 14px!important;
        }
    </style>

    <style>
        .invoice-detail-value{
            font-size: 1rem!important;
        }
        table, th, td {
            border: 1px solid;
            font-size: .8rem!important;
        }

        .table.table-md th, .table.table-md td{
            padding: 4px 10px!important;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="main-wrapper">
            <div class="row">
                <div class="content col-12">
                      <section class="section">
                        <div class="section-body">
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="invoice-title">
                                                <h4>Invoice Transaksi</h4>
                                                <div class="invoice-number" style="font-size: .9rem">Order {{ $data->no_invoice }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <address class="row">
                                                        <div class="col-6">
                                                            <strong>Billed To:</strong><br>
                                                            {{ $alamat_pengiriman->penerima }}<br>
                                                        </div>
                                                        <div class="col-6" style="text-align: end;">
                                                            {{ $alamat_pengiriman->alamat_lengkap }}<br>
                                                            {{ $data->jasa_pengiriman }}
                                                        </div>
                                                    </address>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section-title">Barang Order</div>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-md">
                                                    <tr>
                                                        <th data-width="40">#</th>
                                                        <th>Item</th>
                                                        <th class="text-center" width="18%">Price</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center" width="18%">Total</th>
                                                    </tr>
                                                    @php
                                                    $sub_total = 0;
                                                    @endphp
                                                    @foreach ($data->sales_order_product as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->product_original->product_name }}</td>
                                                        <td class="text-center">{{ "Rp. " . number_format($item->harga,0,',','.') }}</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-center">{{ "Rp. " . number_format($item->total_harga_produk,0,',','.') }}</td>
                                                        @php
                                                        $sub_total += $item->total_harga_produk;
                                                        @endphp
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            <div class="row">
                                               <div class="col-4">
                                                    @if (isset($kode_voucher))
                                                        <div class="section-title voucher-title">Vocher yang digunakan</div>
                                                        <p class="section-lead"><b class="text-primary">{{ $kode_voucher->kode_voucher}}</b></p>
                                                    @endif
                                                </div>
                                                <div class="col-4"></div>
                                                <div class="col-4 text-right">
                                                    <div class="row">
                                                        <div class="table-responsive" style="padding-right: 5%;">
                                                            <table class="table table-md" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" style="border: none">
                                                                <tr>
                                                                    <th style="border: none">Subtotal :</th>
                                                                    <th class="text-left" style="border: none">{{ "Rp. " . number_format($sub_total,0,',','.') }}</th>
                                                                </tr>
                                                                <tr>
                                                                    <th style="border: none">Pengiriman :</th>
                                                                    <th class="text-left" style="border: none">{{ "Rp. " . number_format($data->ongkos_kirim,0,',','.') }}</th>
                                                                </tr>
                                                                <tr>
                                                                    <th style="border: none">Total :</th>
                                                                    <th class="text-left" style="border: none">{{ "Rp. " . number_format($data->total_harga,0,',','.') }}</th>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    {{-- <div class="row">
                                                        <div class="col-6">
                                                            <div class="invoice-detail-item">
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem">Subtotal</div>
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem">Pengiriman</div>
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem">Potensi Cashback</div>
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem; white-space: nowrap;">Potongan Harga Grosir</div>
                                                                <hr class="mt-4" style="margin-bottom: 0">
                                                                <div class="invoice-detail-name h5">Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="invoice-detail-item">
                                                                <div class="invoice-detail-value">{{ "Rp. " . number_format($sub_total,0,',','.') }}</div>
                                                                <div class="invoice-detail-value">{{ "Rp. " . number_format($data->harga_pengiriman,0,',','.') }}</div>
                                                                <div class="invoice-detail-value">{{ "Rp. " . number_format($data->point,0,',','.') }}</div>
                                                                <div class="invoice-detail-value">{{ "Rp. " . number_format($data->potongan_harga_grosir,0,',','.') }}</div>
                                                                <hr style="margin-bottom: 0">
                                                                <div class="invoice-detail-value invoice-detail-value-lg">{{ "Rp. " . number_format($data->total_harga,0,',','.') }}</div>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- <div class="content col-6">
                    <section class="section">
                        <div class="section-body">
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="invoice-title">
                                                <h4>Invoice Transaksi</h4>
                                                <div class="invoice-number" style="font-size: .9rem">Order {{ $data->no_invoice }}</div>
                                            </div>
                                            <hr style="margin: 15px">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <address class="row">
                                                        <div class="col-6">
                                                            <strong>Billed To:</strong><br>
                                                            {{ $data->alamat_pengiriman->penerima }}<br>
                                                        </div>
                                                        <div class="col-6" style="text-align: end;">
                                                            {{ $data->alamat_pengiriman->alamat_lengkap }}<br>
                                                            {{ $data->jasa_pengiriman }}
                                                        </div>
                                                    </address>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section-title" >Barang Order</div>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-md">
                                                    <tr>
                                                        <th data-width="40">#</th>
                                                        <th>Item</th>
                                                        <th class="text-center">Price</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                    @php
                                                    $sub_total = 0;
                                                    @endphp
                                                    @foreach ($data->transaksi_produk as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->master_produk->nama_produk_desc }}</td>
                                                        <td class="text-center">{{ "Rp. " . number_format($item->harga,0,',','.') }}</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-center">{{ "Rp. " . number_format($item->harga * $item->quantity,0,',','.') }}</td>
                                                        @php
                                                        $sub_total += $item->harga * $item->quantity;
                                                        @endphp
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            <div class="row mt-2">
                                                    <div class="col-4">
                                                        @if (isset($kode_voucher))
                                                            <div class="section-title voucher-title">Vocher yang digunakan</div>
                                                            <p class="section-lead"><b class="text-primary">{{ $kode_voucher->kode_voucher}}</b></p>
                                                        @endif
                                                    </div>
                                                <div class="col-8 text-right">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="invoice-detail-item">
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem">Subtotal</div>
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem">Pengiriman</div>
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem">Potensi Cashback</div>
                                                                <div class="invoice-detail-name" style="margin-top: .4rem; margin-bottom: .8rem; white-space: nowrap;">Potongan Harga Grosir</div>
                                                                <hr class="mt-4" style="margin-bottom: 0">
                                                                <div class="invoice-detail-name h5">Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="invoice-detail-item">
                                                                <div class="invoice-detail-value mt-2">{{ "Rp. " . number_format($sub_total,0,',','.') }}</div>
                                                                <div class="invoice-detail-value mt-2">{{ "Rp. " . number_format($data->harga_pengiriman,0,',','.') }}</div>
                                                                <div class="invoice-detail-value mt-2">{{ "Rp. " . number_format($data->point,0,',','.') }}</div>
                                                                <div class="invoice-detail-value mt-2">{{ "Rp. " . number_format($data->potongan_harga_grosir,0,',','.') }}</div>
                                                                <hr style="margin-top: 27px; margin-bottom: 0">
                                                                <div class="invoice-detail-value invoice-detail-value-lg">{{ "Rp. " . number_format($data->total_harga,0,',','.') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div> --}}
            </div>
        </div>
    </div>
</body>
</html>
<script src="{{ asset('assets/modules/jquery.min.js') }}"></script>

<script>
    $(document).ready(function(){
        window.print().trigger('click');
    })
</script>
