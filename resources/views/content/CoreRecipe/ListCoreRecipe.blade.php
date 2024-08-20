@inject('CR', 'App\Http\Controllers\CoreRecipeController')

@extends('adminlte::page')

@section('title', 'MOZAIC Holyville')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Menu</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Menu</b> <small>Kelola Menu</small>
</h3>
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
        {{-- <button onclick="location.href='{{ url('/recipe/add-recipe') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Barang </button> --}}
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Nama Kategori Menu</th>
                        <th width="20%" style='text-align:center'>Kode Menu</th>
                        <th width="20%" style='text-align:center'>Nama Menu</th>
                        <th width="15%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $row['item_category_name'] }}</td>
                        <td>{{ $row['item_code'] }}</td>
                        <td>{{ $row['item_name'] }}</td>
                        <td class="text-center">
                            <a type="button" class="btn btn-info btn-sm" href="{{ url('/recipe/add-recipe/'.$row['item_id'] ) }}"><i class="fa fa-file" aria-hidden="true"></i> &nbsp; Resep</a>
                            <a href='#addNewItem_{{ $no }}' data-toggle='modal' name="Find" class="btn btn-success btn-sm" title="Add Data"><i class="fa fa-spinner" aria-hidden="true"></i> Proses</a>
                            {{-- <div class="btn btn-success btn-sm" data-toggle="modal" data-target="#addNewItem"><i class="fa fa-spinner" aria-hidden="true"></i> &nbsp; Proses</div> --}}

                        </td>
                    </tr>

                    <div class="modal fade bs-modal-md" id="addNewItem_{{ $no }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <form method="post" action="{{ route('process-recipe') }}" enctype="multipart/form-data">
                            @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addNewItemLabel">Tambah Proses</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                                <div class="row form-group">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <a class="text-dark">Jumlah<a class='red'> *</a></a>
                                                            <input class="form-control input-bb" name="item_code" id="item_code" type="text" autocomplete="off" value=" {{ $row['item_id'] }}" hidden/>
                                                            <input class="form-control input-bb" name="qty" id="qty" type="text" autocomplete="off" value="" />

                                                        @foreach ($CR->getRecipe($row['item_id']) as $val )
                                                            <input class="form-control input-bb" name="item_id" id="item_id" type="text" autocomplete="off" value=" {{ $val['item_id'] }}" hidden/>
                                                            <input class="form-control input-bb" name="quantity" id="quantity" type="text" autocomplete="off" value=" {{ $val['quantity'] }}" hidden/>
                                                        @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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