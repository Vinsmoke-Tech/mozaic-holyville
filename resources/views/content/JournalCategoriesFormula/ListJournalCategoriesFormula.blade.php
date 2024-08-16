@inject('SystemUser', 'App\Http\Controllers\SystemUserController')

@extends('adminlte::page')

@section('title', 'MOZAIC Waroeng Jamboel')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Jurnal Khusus</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Jurnal Khusus</b> <small>Kelola Jurnal Khusus </small>
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
        <button onclick="location.href='{{ url( 'journal-categories-formula/'.$journal_categories_id .'/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Jurnal Khusus </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="20%" style='text-align:center'>Nama Jurnal Khusus</th>
                        <th width="20%" style='text-align:center'>Deskripsi</th>
                        <th width="20%" style='text-align:center'>Nama Account</th>
                        <th width="20%" style='text-align:center'>D/K</th>


                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $row['journal_categories_item_formula_name'] }}</td>
                        <td>{{ $row['journal_categories_item_formula_description'] }}</td>
                        <td>{{ $row['account_id'] }}</td>
                        <td>{{ $row['account_id_status'] }}</td>
                    <td class="">
                        {{-- <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/journal-categories/edit/'.$row['journal_categories_id']) }}">List Jurnal</a> --}}
                        <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/journal-categories-formula/edit/'.$row['journal_categories_id']) }}">Edit</a>
                        <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/journal-categories-formula/delete/'.$row['journal_categories_id']) }}">Hapus</a>
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