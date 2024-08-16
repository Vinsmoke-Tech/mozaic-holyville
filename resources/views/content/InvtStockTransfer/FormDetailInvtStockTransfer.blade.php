@inject('StockTransfer','App\Http\Controllers\InvtStockTransferController')
@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/stock-transfer') }}">Daftar Pengguanaan Bahan Baku</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pengguanaan Bahan Baku</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Detail Pengguanaan Bahan Baku
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
<form  method="post" action="{{ route('process-add-stock-transfer') }}" enctype="multipart/form-data">
@csrf
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Detail
            </h5>
            <div class="float-right">
                <a onclick="location.href='{{ url('stock-transfer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</a>
            </div>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class ="row">
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb" type="text" value="{{ date('d-m-Y', strtotime($data['transfer_date'])) }}" readonly>
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">No. Penggunaan
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb" type="text" value="{{ $data['transfer_no'] }}" readonly>
                        </div>
                    </div>
                    <div class ="col-md-9">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Keterangan
                            </section>
                            <textarea class="form-control input-bb" type="text" readonly>{{ $data['transfer_remark'] }}</textarea>
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
        <h5 class="text-bold">Bahan Baku</h5>
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
                    @if (!empty($dataItem))
                        @if (array_search('ingredient', array_column($dataItem, 'type')) !== false)
                            @foreach ($dataItem as $key => $val)
                                @if ($val['type'] == 'ingredient')
                                    <tr>
                                        <td class="text-center">{{ $no++ }}.</td>
                                        <td>{{ $StockTransfer->getCategoryName($val['item_category_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemName($val['item_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemUnitName($val['item_unit_id']) }}</td>
                                        <td class="text-right">{{ $val['quantity'] }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <td colspan="6" class="text-bold text-center">Data Kosong</td>
                        @endif
                    @else
                        <td colspan="6" class="text-bold text-center">Data Kosong</td>
                    @endif
                </tbody>
            </table>
        </div>
        <h5 class="text-bold mt-5">Menu Jadi</h5>
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
                    @if (!empty($dataItem))
                        @if (array_search('menu', array_column($dataItem, 'type')) !== false)
                            @foreach ($dataItem as $key => $val)
                                @if ($val['type'] == 'menu')
                                    <tr>
                                        <td class="text-center">{{ $no++ }}.</td>
                                        <td>{{ $StockTransfer->getCategoryName($val['item_category_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemName($val['item_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemUnitName($val['item_unit_id']) }}</td>
                                        <td class="text-right">{{ $val['quantity'] }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <td colspan="6" class="text-bold text-center">Data Kosong</td>
                        @endif
                    @else
                        <td colspan="6" class="text-bold text-center">Data Kosong</td>
                    @endif
                </tbody>
            </table>
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