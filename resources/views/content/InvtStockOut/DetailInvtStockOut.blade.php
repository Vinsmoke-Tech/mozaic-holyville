@inject('StockOut','App\Http\Controllers\InvtStockOutController')
@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/stock-out') }}">Daftar Pengeluaran Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pengeluaran Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
     Detail Pengeluaran Barang
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif

@if(count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
    @endforeach
</div>
@endif
<form  method="post" action="{{ route('process-add-stock-out') }}" enctype="multipart/form-data">
@csrf
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Detail
            </h5>
            <div class="float-right">
                <a onclick="location.href='{{ url('stock-out') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</a>
            </div>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class ="row">
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="stock_out_date" id="stock_out_date" value="{{ $data['stock_out_date'] }}" disabled/>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nomor Pengeluaran
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="text" class="form-control input-bb" name="stock_out_no" id="stock_out_no" value="{{ $data['stock_out_no'] }}" disabled/>
                        </div>
                    </div>
                    <div class ="col-md-9">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Keterangan
                            </section>
                            <textarea class="form-control input-bb" name="stock_out_remark" id="stock_out_remark" type="text" autocomplete="off" disabled>{{ $data['stock_out_remark'] }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
    </div>
    <div class="card-body">
        <div class="form-body form">
            <div class="table-responsive">
                <table class="table table-bordered table-advance table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style='text-align:center; width: 5%'>No</th>
                            <th style='text-align:center; width: 20%'>Nama Kategori</th>
                            <th style='text-align:center; width: 20%'>Nama Barang</th>
                            <th style='text-align:center; width: 20%'>Nama Satuan</th>
                            <th style='text-align:center; width: 20%'>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;?>
                        @if (!empty($data_item))
                            @foreach ($data_item as $key=>$val)
                            <tr>
                                <td class="text-center">{{ $no++ }}.</td>
                                <td>{{ $StockOut->getCategoryName($val['item_category_id']) }}</td>
                                <td>{{ $StockOut->getItemName($val['item_id']) }}</td>
                                <td>{{ $StockOut->getItemUnitName($val['item_unit_id']) }}</td>
                                <td class="text-right">{{ $val['quantity'] }}</td>
                            </tr>
                            @endforeach
                        @elseif ($data_array == [])
                            <td colspan="6" class="text-bold text-center">Data Kosong</td>
                        @else
                            <td colspan="6" class="text-bold text-center">Data Kosong</td>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</form>
@stop

@section('footer')
    
@stop

@section('css')
    
@stop