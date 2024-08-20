@inject('PurchaseInvoice','App\Http\Controllers\PurchaseInvoiceController')
@extends('adminlte::page')

@section('title', 'MOZAIC Holyville')
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('filter-reset-purchase-invoice')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-purchase-invoice')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    $(document).ready(function(){
      
      var purchase_method = {!! json_encode(session('purchase_method')) !!};
      if (purchase_method == null) {
          $('#purchase_method').select2('val', '');
      }
    
  });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pembelian</b> <small>Kelola Pembelian </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-purchase-invoice') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date }}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date }}" style="width: 15rem;"/>
                        </div>
                    </div>


                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Metode Pembelian
                                <span class="required text-danger">
                                    *
                                </span>
                                {!! Form::select('', $purchase_method_list, $purchase_method, ['class' => 'form-control selection-search-clear select-form', 'id' => 'purchase_method','name' => 'purchase_method']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ url('/purchase-invoice/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Pembelian </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style="text-align: center">No </th>
                        <th style="text-align: center">No Pembelian</th>
                        <th style="text-align: center">Tanggal Pembelian</th>
                        <th style="text-align: center">Nama Pemasok</th>
                        <th style="text-align: center">Metode</th>
                        <th style="text-align: center">Jumlah Pembelian</th>	
	
                        <th style="text-align: center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach ($data as $row )
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row['purchase_invoice_no'] }}</td>
                        <td>{{ date('d-m-Y', strtotime($row['purchase_invoice_date'])) }}</td>
                        <td>{{ $row['purchase_invoice_supplier'] }}</td>
                        <td>{{ $purchase_method_list [$row['purchase_method']] }}</td>
                        <td style="text-align: right">{{ number_format($row['total_amount'],2,',','.') }}</td>

                        <td class="text-center">

                            <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/purchase-invoice/detail/'.$row['purchase_invoice_id']) }}">Detail</a>
                            @if ($row->purchase_method==1&&$row->payment_status==0)
                                <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/purchase-invoice/delete/'.$row['purchase_invoice_id']) }}" onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')">Hapus</a>
                            @endif

                            @if ($row->purchase_method==2&&$row->payment_status==0)
                                <a type="button" class="btn btn-outline-info btn-sm" href="{{ url('/purchase-invoice/paid-detail-purchase-invoice/'.$row['purchase_invoice_id']) }}">Bayar</a>
                                <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/purchase-invoice/delete-consignee/'.$row['purchase_invoice_id']) }}" onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')">Hapus Konsinyasi</a>
                                <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/purchase-invoice/reset-consignee/'.$row['purchase_invoice_id']) }}" onclick="return confirm('Reset Stok ?')">Reset</a>

                            @elseif($row->purchase_method==2&&$row->payment_status==1)
                                <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/purchase-invoice/paid-detail-purchase-invoice/'.$row['purchase_invoice_id']) }}">Lunas</a>
                            @else
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop   