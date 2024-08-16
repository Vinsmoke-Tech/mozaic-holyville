@inject('ISAC','App\Http\Controllers\InvtStockAdjustmentController')
@extends('adminlte::page')

@section('title', 'MOZAIC Waroeng Jamboel')
@section('js')
<script>
      function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-purchase-return')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    function function_last_balance_physical(value, id){
        var last_data =  parseFloat(document.getElementById("last_balance_data_" + id).value) || 0;
        var last_adjustment = parseFloat(value) || 0;
        
        var last_physical = last_adjustment - last_data;
        $('#last_balance_physical_' + id).val(last_physical);
    }

    function handleChange($no) {
        console.log($no);
        var checkbox = $('#checkbox_view_' + $no);
        var lastBalanceAdjustmentField = $('#last_balance_adjustment_' + $no);

        // $("#checkbox_"+$no).val("0");

        if (checkbox.is(":checked")) {
            lastBalanceAdjustmentField.prop('readonly', false);
            $("#checkbox_" + $no).val("1");
        } else {
            lastBalanceAdjustmentField.prop('readonly', true);
            $("#checkbox_" + $no).val("0");
        }
    }

    $(document).ready(function() {
        $(':checkbox').attr('checked', false);
             // $("checkbox").val(1);
    });


    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-stock-adjustment')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    $(document).ready(function() {
        var category_id     = {!! json_encode($category_id) !!};
        if (category_id == "") {
            $('#item_category_id').select2('val',' ');
        }
    });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('stock-adjustment') }}">Daftar Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Penyesuaian Stok</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Penyesuaian Stok
</h3>
<br/>

{{-- pop up --}}
@if(session('msg'))
    @if(strpos(session('msg'), 'Gagal') !== false)
        <div class="alert alert-danger" role="alert">
            {{ session('msg') }}
        </div>
    @else
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif
@endif

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('stock-adjustment') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>
    <form method="post" action="{{ route('filter-add-stock-adjustment') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-12">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        {!! Form::select('item_category_id',  $categorys, $category_id, ['class' => 'selection-search-clear select-form', 'id' => 'item_category_id', 'name' => 'item_category_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
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
    </form>    
</div>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
    </div>
    <form method="POST" action="{{ route('process-add-stock-adjustment') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                        <thead>
                            <tr>
                                <th style='text-align:center'>Nama Barang</th>
                                <th style='text-align:center'>Satuan Barang</th>
                                <th style='text-align:center'>Gudang</th>
                                <th style='text-align:center'>Stock Sistem</th>
                                <th style='text-align:center'>Stock Fisik</th>
                                <th style='text-align:center'>Selisih Stock</th>
                                <th style='text-align:center'>Keterangan</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; $total_no = 1; ?>
                                @foreach ($data as $row)
                                    <tr>
                                        <td>
                                            {{ $ISAC->getItemName($row['item_id']) }}
                                            <input type="text" name="item_id_{{ $no }}" id="item_id" value="{{ $row['item_id'] }}" hidden>
                                            <input type="text" name="item_category_id_{{ $no }}" id="item_category_id" value="{{ $row['item_category_id'] }}" hidden>
                                            <input type="text" name="item_stock_id_{{ $no }}" id="item_stock_id_{{ $no }}" value="{{ $row['item_stock_id'] }}" hidden >

                                        </td>
                                        <td>
                                            {{ $ISAC->getItemUnitName($row['item_unit_id']) }}
                                            <input type="text" name="item_unit_id" id="item_unit_id" value="{{ $row['item_unit_id'] }}" hidden>
                                        </td>
                                        <td>
                                            {{ $ISAC->getWarehouseName($row['warehouse_id']) }}
                                            <input type="text" name="warehouse_id" id="warehouse_id" value="{{ $row['warehouse_id'] }}" hidden>
                                        </td>
                                        <td style='text-align:right'>
                                            {{ $ISAC->getItemStock($row['item_id'],$row['item_category_id']) }}
                                            <input type="text" name="last_balance_data_{{ $no }}" id="last_balance_data_{{ $no }}" value="{{ $ISAC->getItemStock($row['item_id'],$row['item_category_id']) }}"  hidden>
                                        </td>
                                        <td style="text-align: center">
                                            <input style="text-align: right" class="form-control input-bb" type="text" name="last_balance_adjustment_{{ $no }}" id="last_balance_adjustment_{{ $no }}" onchange="function_last_balance_physical(this.value, '{{ $no }}')" autocomplete="off" readonly>
                                        </td>
                                        <td style="text-align: center">
                                            <input style="text-align: right" class="form-control input-bb" type="text" name="last_balance_physical_{{ $no }}" id="last_balance_physical_{{ $no }}" readonly>
                                        </td>
                                        
                                        <td style="text-align: center">
                                            <input class="form-control input-bb" type="text" name="stock_adjustment_item_remark" id="stock_adjustment_item_remark" autocomplete="off">
                                        </td>
                                        <td style='text-align:center'>
                                            <input type='checkbox' class='form-control' name='checkbox_view_{{$no}}' id='checkbox_view_{{$no}}' onchange="handleChange({{ $no }})"/>
                                            <input class='form-control' type='text' name='checkbox_{{$no}}' id='checkbox_{{ $no }}' value="0" hidden/> 
                                        </td>
                                    </tr>
                                    @php
                                        $total_no = $no;
                                        $no++;
                                    @endphp
                                @endforeach
                                </tbody>
                                <input class='form-control' style='text-align:right;'type='text' name='total_no' id='total_no' value='{{$total_no}}' hidden/>
                            </table>
                            <br />
                                <div class="card-footer text-muted">
                                    <div class="form-actions float-right">
                                        <button type="submit" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
                                    </div>
                                </div>
                </div>
            </div>
        </div>
    </form>
</div>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop