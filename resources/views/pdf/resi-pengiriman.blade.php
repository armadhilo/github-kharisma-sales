<html>
	<head>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<style>
			@page {
				margin: 8px 40px;
			}
	
			* {
				font-family: Arial, Helvetica, sans-serif;
			}
			
			body{
				/* background-image: url("{{ asset('assets/img/project/background.png') }}");
				background-position: center;
				background-repeat: no-repeat;
				background-size: 50%; */
				width:100%;
				height:90%;
				padding: 10px 0px 10px 0px;
				margin:0;
			}
	
			p, label {
				font-size: 12px;
			}
	
			.table{
				font-size: 12px;
				text-align: justify;
			}
	
			.table th{
				padding: 2px 0px 2px 0px;
				line-height: 1.5;
				font-size: 12px;
			}
	
			.table td{
				padding: 1px 4px;
				line-height: 1.5;
				font-size: 12px;
			}
		</style>
	</head>

	<body>
		<div id="tb">
            <table class="table" width="50%" border="1" bordercolor="#000000" style="border-collapse: collapse;" cellpadding="2">
                <tr style="height: 90px">
                    <td style="text-align: center; vertical-align:middle" width='50%'><img src="https://kharismastationerykupang.com/assets/img/kharisma_logo.png" alt="" style="padding: 5px 0" width="30%"></td>
                    <td width='50%' style="text-align: center; vertical-align:middle">
						<h4><b>{{ $transaksi->jasa_pengiriman }}</b></h4>
					</td>
                </tr>
				<tr>
					<td colspan="2">
						<table style="border-spacing:0 7px;">
							<tr style="margin-top">
								<td width="20%" style="vertical-align: top; border: none"> <b>No Invoice</b> </td>
								<td style="border: none">{{ $transaksi->no_invoice }}</td>
							</tr>
							<tr style="margin-top">
								<td width="20%" style="vertical-align: top; border: none"><b>Kepada</b> </td>
								<td style="border: none">
									<b>{{ $alamat_pengiriman->penerima }}</b><br>
									<span>{{ $alamat_pengiriman->alamat . ', ' . $alamat_pengiriman->kecamatan . ', ' . $alamat_pengiriman->kelurahan }} <br> {{ $alamat_pengiriman->provinsi }} - {{ $alamat_pengiriman->kota }} [{{ $alamat_pengiriman->kode_pos }}] <br> {{ $alamat_pengiriman->no_telepon }} </span>
								</td>
							</tr>
							<tr style="margin-top">
								<td width="20%" style="vertical-align: top; border: none"><b>Dari</b></td>
								<td style="border: none">
									<b>Kharisma Online</b><br>
									<span>{{ $asal_pengiriman->alamat }} <br> {{ $asal_pengiriman->provinsi }} - {{ $asal_pengiriman->kota }} [{{ $asal_pengiriman->kode_pos }}] <br> {{ $asal_pengiriman->no_telepon }} </span>
								</td>
							</tr>
							<tr>
								<td style="border: none"></td>
								<td style="border: none">
									<table style="border-style: dashed;">
										<tr>
											<td style="border: none">Total Berat : <b>{{ $transaksi->total_berat }} gr</b></td>
											<td style="border: none">
												Bea Kirim : <b>{{"Rp " . number_format($transaksi->ongkos_kirim,0,',','.'); }} </b>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="border:none; border-top: 1px dotted black;">
									<b>Item Pesanan</b> 
								</td>
							</tr>
							@foreach ($transaksi->sales_order_product as $item)
								<tr>
									<td style="border: none; font-size:10px">{{ $item->quantity ." ". $item->product_original->unit}}</td>
									<td style="border: none; font-size:10px">{{ $item->product_original->product_name }}</td>
								</tr>
							@endforeach
						</table>
					</td>
				</tr>
            </table>
		</div>
	</body>
</html>
