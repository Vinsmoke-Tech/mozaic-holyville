@inject('StockOut','App\Http\Controllers\InvtStockOutController')
@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-stock-out')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    function processAddArrayStockOut(){
        var item_category_id    = document.getElementById("item_category_id").value;
        var item_id		        = document.getElementById("item_id").value;
        var item_unit_id		= document.getElementById("item_unit_id").value;
        var quantity            = document.getElementById("quantity").value;
        var default_quantity    = document.getElementById("default_quantity").value;

        $.ajax({
            type: "POST",
            url : "{{route('add-array-stock-out')}}",
            data: {
                'item_category_id'  : item_category_id,
                'item_id'    	    : item_id, 
                'item_unit_id'      : item_unit_id,
                'quantity'          : quantity,
                'default_quantity'  : default_quantity,
                '_token'            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-delete-elements-stock-out')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    $(document).ready(function(){
        $("#item_category_id").select2("val", "0");
        $("#item_unit_id").select2("val", "0");
        $("#item_id").select2("val", "0");

        $("#item_category_id").change(function(){
            $("#item_unit_id").select2("val", "0");
            $("#item_id").select2("val", "0");
            $('#default_quantity').val('');
            $('#quantity').val('');
			var id 	= $("#item_category_id").val();
            $.ajax({
                url: "{{ url('select-item') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_id').html(data);

                }
            });
		});

        $("#item_id").change(function(){
            $("#item_unit_id").select2("val", "0");
            $('#default_quantity').val('');
            $('#quantity').val('');
			var id 	= $("#item_id").val();
            $.ajax({
                url: "{{ url('select-item-unit') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_unit_id').html(data);

                }
            });
		});

        $("#item_unit_id").change(function(){
			var category_id = $("#item_category_id").val();
            var unit_id = $("#item_unit_id").val();
            var item_id = $("#item_id").val();
            $('#default_quantity').val('');
            $('#quantity').val('');
            $.ajax({
                url: "{{ url('select-item-stock') }}"+'/'+category_id+'/'+unit_id+'/'+item_id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    // console.log(data);
                    if (data != "") {
                        $('#default_quantity').val(data);
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
        <li class="breadcrumb-item"><a href="{{ url('/stock-out') }}">Daftar Pengeluaran Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Pengeluaran Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Pengeluaran Barang
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
                Form Tambah
            </h5>
            <div class="float-right">
                <a onclick="location.href='{{ url('stock-out') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</a>
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
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="stock_out_date" id="stock_out_date" value="{{ $datases['stock_out_date'] == '' ? date('Y-m-d') : $datases['stock_out_date'] }}" onchange="function_elements_add(this.name, this.value)" style="width: 15rem;"/>
                        </div>
                    </div>
                    <div class ="col-md-9">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Keterangan
                            </section>
                            <textarea class="form-control input-bb" name="stock_out_remark" id="stock_out_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $datases['stock_out_remark'] }}</textarea>
                        </div>
                    </div>
                    <div class ="col-md-12 mt-5">
                        <h5 class="text-bold">Tambah Barang</h5>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Kategori
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            {!! Form::select('item_category_id', $category_list, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_category_id', 'name' => 'item_category_id']) !!}
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Barang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select name="item_id" id="item_id" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class ="col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Satuan
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select name="item_unit_id" id="item_unit_id" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jumlah
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb text-right" name="quantity" id="quantity" type="text" autocomplete="off" value=""/>
                        </div>
                    </div>
                    <div class ="col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jumlah Tersedia
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input class="form-control input-bb text-right" name="default_quantity" id="default_quantity" type="text" autocomplete="off" value="" disabled/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a type="button" onclick="processAddArrayStockOut();" name="Add" class="btn btn-success" title="Add Data">Tambah</a>
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
                            <th style='text-align:center; width: 10%'>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;?>
                        @if (!empty($data_array))
                            @foreach ($data_array as $key=>$val)
                            <tr>
                                <td class="text-center">{{ $no++ }}.</td>
                                <td>{{ $StockOut->getCategoryName($val['item_category_id']) }}</td>
                                <td>{{ $StockOut->getItemName($val['item_id']) }}</td>
                                <td>{{ $StockOut->getItemUnitName($val['item_unit_id']) }}</td>
                                <td class="text-right">{{ $val['quantity'] }}</td>
                                <td class="text-center">
                                    <a href="{{route('add-delete-array-stock-out', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i> Hapus</a>
                                </td>
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