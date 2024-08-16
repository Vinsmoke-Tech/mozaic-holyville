@extends('adminlte::page')

@section('title', 'MOZAIC Waroeng Jamboel')
@section('js')
{{-- <script>
    function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "{{route('add-item-unit-elements')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                },
				success: function(msg){
			}
		});
	}

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-item-unit')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}
</script> --}}
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('item-category') }}">Daftar Kategori Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah Kategori Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Kategori Barang
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
@endif
</div>
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Ubah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('journal-categories') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('journal-categories.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="journal_categories_id" id="journal_categories_id" type="text" autocomplete="off" value="{{ $data['journal_categories_id'] }}" hidden/>
                        <input class="form-control input-bb" name="journal_categories_name" id="journal_categories_name" type="text" autocomplete="off" value="{{ $data['journal_categories_name'] }}{{ old('category_name') }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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