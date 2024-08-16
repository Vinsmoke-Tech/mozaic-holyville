@inject('CRA','App\Http\Controllers\CoreRecipeController')
@extends('adminlte::page')

@section('title', 'MOZAIC Waroeng Jamboel')
@section('js')
<script>
 function changeSatuan(){
            var item_id = $("#item_id").val();

            $.ajax({
                type: "POST",
                url: "{{ route('get-item-unit') }}",
                dataType: "html",
                data: {
                    'item_id': item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    $('#item_unit_id').val(1);
                    $('#item_unit_id').html(return_data);
                    // function_elements_add('item_id', item_id);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        $(document).ready(function() {
            changeSatuan(); 
        });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('recipe') }}">Daftar Resep</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Resep</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Resep
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
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('recipe') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ route('process-add-recipe') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Menu<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="item_name" id="item_name" type="text" autocomplete="off" value="{{ $items['item_name'] }}"/>
                        <input class="form-control input-bb" name="item_menu_id" id="item_menu_id" type="text" autocomplete="off" value="{{ $items['item_id'] }}" hidden/>
                    </div>
                </div>

            <h6 class="col-md-8 mt-4 mb-3"><b>Data Bahan Resep</b></h6>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                        {!! Form::select('item_id', $items2, 0, ['class' => 'selection-search-clear select-form', 'name'=>'item_id'  ,'id' => 'item_id','onchange'=>'changeSatuan()']) !!}
                        
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Satuan<a class='red'> *</a></a>
                    <select class="selection-search-clear required select-form"
                    placeholder="Masukan Kategori Barang" name="item_unit_id" id="item_unit_id">
                </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Jumlah <a class='red'> *</a></a>
                        <input class="form-control input-bb" name="quantity" id="quantity" type="text" autocomplete="off" value=""/>
                    </div>
                </div>
            </div>
        </div>
        
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-plus"></i> Tambah </button>
                </div>
            </div>
        </form>
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
                                <th style='text-align:center'>No</th>
                                <th style='text-align:center'>Nama Resep</th>
                                <th style='text-align:center'>Satuan</th>
                                <th style='text-align:center'>Jumlah</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            @foreach($data as $row)
                                    <tr>
                                                    <td style='text-align  : center !important;''>{{ $no++ }}</td>
                                                    <td style='text-align  : left !important;'>{{$CRA->getName($row['item_id'])  }}</td>
                                                    <td style='text-align  : left !important;'>{{ $CRA->getUnit($row['item_unit_id']) }}</td>
                                                    <td style='text-align  : right !important;'>{{ $row['quantity'] }}</td>
                                                    <td style='text-align  : center !important;'>   <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/recipe/delete-recipe/'.$row['recipe_id']) }}">Hapus</a> </button>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            {{-- <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Reset Data</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div> --}}
        </div>
</div>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop