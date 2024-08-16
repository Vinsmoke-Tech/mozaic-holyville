@inject('StockTransfer','App\Http\Controllers\InvtStockTransferController')
@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')
@section('js')
<script>
    function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-stock-transfer')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-delete-elements-stock-transfer')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    function addIngredient(){
        var item_category_id    = $("#item_category_id1").val();
        var item_unit_id        = $("#item_unit_id1").val();
        var item_id             = $("#item_id1").val();
        var quantity            = $('#quantity1').val();

        $.ajax({
            type: "POST",
            url : "{{route('add-array-stock-transfer')}}",
            data: {
                'item_category_id'  : item_category_id,
                'item_id'    	    : item_id, 
                'item_unit_id'      : item_unit_id,
                'quantity'          : quantity,
                'type'              : 'ingredient',
                '_token'            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

    function addMenu(){
        var item_category_id    = $("#item_category_id2").val();
        var item_unit_id        = $("#item_unit_id2").val();
        var item_id             = $("#item_id2").val();
        var quantity            = $('#quantity2').val();

        $.ajax({
            type: "POST",
            url : "{{route('add-array-stock-transfer')}}",
            data: {
                'item_category_id'  : item_category_id,
                'item_id'    	    : item_id, 
                'item_unit_id'      : item_unit_id,
                'quantity'          : quantity,
                'type'              : 'menu',
                '_token'            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

    $(document).ready(function(){
        $("#item_category_id1").select2("val", "0");
        $("#item_unit_id1").select2("val", "0");
        $("#item_id1").select2("val", "0");
        $("#item_category_id2").select2("val", "0");
        $("#item_unit_id2").select2("val", "0");
        $("#item_id2").select2("val", "0");

        $("#item_category_id1").change(function(){
            $("#item_unit_id1").select2("val", "0");
            $("#item_id1").select2("val", "0");
            $('#last_stock1').val('');
            $('#quantity1').val('');
			var id 	= $("#item_category_id1").val();
            $.ajax({
                url: "{{ url('select-item') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_id1').html(data);

                }
            });
		});

        $("#item_id1").change(function(){
            $("#item_unit_id1").select2("val", "0");
            $('#last_stock1').val('');
            $('#quantity1').val('');
			var id 	= $("#item_id1").val();
            $.ajax({
                url: "{{ url('select-item-unit') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_unit_id1').html(data);

                }
            });
		});
        
        $("#item_unit_id1").change(function(){
            var category_id = $("#item_category_id1").val();
            var unit_id = $("#item_unit_id1").val();
            var item_id = $("#item_id1").val();
            $('#last_stock1').val('');
            $('#quantity1').val('');
            $.ajax({
                url: "{{ url('select-item-stock') }}"+'/'+category_id+'/'+unit_id+'/'+item_id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    if (data != "") {
                        $('#last_stock1').val(data);
                    }
                }
            });
		});

        $("#item_category_id2").change(function(){
            $("#item_unit_id2").select2("val", "0");
            $("#item_id2").select2("val", "0");
            $('#last_stock2').val('');
            $('#quantity2').val('');
            var id 	= $("#item_category_id2").val();
            $.ajax({
                url: "{{ url('select-item') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_id2').html(data);
                }
            });
        });
    
        $("#item_id2").change(function(){
            $("#item_unit_id2").select2("val", "0");
            $('#quantity2').val('');
            $('#last_stock2').val('');
            var id 	= $("#item_id2").val();
            $.ajax({
                url: "{{ url('select-item-unit') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_unit_id2').html(data);
    
                }
            });
        });

        $("#item_unit_id2").change(function(){
            var category_id = $("#item_category_id2").val();
            var unit_id = $("#item_unit_id2").val();
            var item_id = $("#item_id2").val();
            $('#last_stock2').val('');
            $('#quantity2').val('');
            $.ajax({
                url: "{{ url('select-item-stock') }}"+'/'+category_id+'/'+unit_id+'/'+item_id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    if (data != "") {
                        $('#last_stock2').val(data);
                    }
                }
            });
		});
	});
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/stock-transfer') }}">Daftar Pengguanaan Bahan Baku</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Pengguanaan Bahan Baku</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Pengguanaan Bahan Baku
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
                Form Tambah
            </h5>
            <div class="float-right">
                <a onclick="location.href='{{ url('stock-transfer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</a>
            </div>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class ="row">
                    <div class ="col-md-12">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="transfer_date" id="transfer_date" value="{{ $datases['transfer_date'] == '' ? date('Y-m-d') : $datases['transfer_date'] }}" onchange="function_elements_add(this.name, this.value)" style="width: 15rem;"/>
                        </div>
                    </div>
                    <div class ="col-md-9">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Keterangan
                            </section>
                            <textarea class="form-control input-bb" name="transfer_remark" id="transfer_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $datases['transfer_remark'] }}</textarea>
                        </div>
                    </div>
                    <div class ="col-md-12 mt-5">
                        <h5 class="text-bold">Bahan Baku</h5>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Kategori
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            {!! Form::select('item_category_id1', $category_list, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_category_id1']) !!}
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Barang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select id="item_id1" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Satuan
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select id="item_unit_id1" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jumlah
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb text-right" id="quantity1" type="text" autocomplete="off" value=""/>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jumlah Tersedia
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb text-right" name="last_stock1" id="last_stock1" type="text" autocomplete="off" value="" disabled/>
                        </div>
                    </div>
                    <div class="col-md-12 text-right mt-2">
                        <a type="button" onclick="addIngredient();" class="btn btn-success">Tambah</a>
                    </div>
                    <div class ="col-md-12 mt-5">
                        <h5 class="text-bold">Menu Jadi</h5>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Kategori
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            {!! Form::select('item_category_id', $category_list, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_category_id2']) !!}
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Barang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select id="item_id2" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Satuan
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select id="item_unit_id2" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jumlah
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb text-right" id="quantity2" type="text" autocomplete="off" value=""/>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jumlah Tersedia
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb text-right" name="last_stock2" id="last_stock2" type="text" autocomplete="off" value="" disabled/>
                        </div>
                    </div>
                    <div class="col-md-12 text-right mt-2">
                        <a type="button" onclick="addMenu();" class="btn btn-success">Tambah</a>
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
                        <th style='text-align:center; width: 10%'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;?>
                    @if (!empty($dataArray))
                        @if (array_search('ingredient', array_column($dataArray, 'type')) !== false)
                            @foreach ($dataArray as $key => $val)
                                @if ($val['type'] == 'ingredient')
                                    <tr>
                                        <td class="text-center">{{ $no++ }}.</td>
                                        <td>{{ $StockTransfer->getCategoryName($val['item_category_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemName($val['item_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemUnitName($val['item_unit_id']) }}</td>
                                        <td class="text-right">{{ $val['quantity'] }}</td>
                                        <td class="text-center">
                                            <a href="{{route('add-delete-array-stock-transfer', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i> Hapus</a>
                                        </td>
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
                        <th style='text-align:center; width: 10%'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;?>
                    @if (!empty($dataArray))
                        @if (array_search('menu', array_column($dataArray, 'type')) !== false)
                            @foreach ($dataArray as $key => $val)
                                @if ($val['type'] == 'menu')
                                    <tr>
                                        <td class="text-center">{{ $no++ }}.</td>
                                        <td>{{ $StockTransfer->getCategoryName($val['item_category_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemName($val['item_id']) }}</td>
                                        <td>{{ $StockTransfer->getItemUnitName($val['item_unit_id']) }}</td>
                                        <td class="text-right">{{ $val['quantity'] }}</td>
                                        <td class="text-center">
                                            <a href="{{route('add-delete-array-stock-transfer', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i> Hapus</a>
                                        </td>
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
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Reset Data</button>
            <button type="submit" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
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