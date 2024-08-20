@inject('SIBIRC','App\Http\Controllers\SalesInvoicebyItemReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Holyville')
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('filter-reset-sales-invoice-by-item-not-sold-report')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Barang Yang Tidak Terjual</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Barang Yang Tidak Terjual</b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-sales-invoice-by-item-not-sold-report') }}" enctype="multipart/form-data">
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
                    
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add()" ><i class="fa fa-times"></i> Batal</button>
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
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center; width: 30%'>Nama Kategori</th>
                        <th style='text-align:center; width: 30%'>Nama Barang</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; ?>
                    <?php
                        if (empty($data)) {
                            echo "";
                        }else {
                           foreach ($data as $key => $row) {
                            echo "<tr>
                                    <td>". $no++."</td>
                                    <td>". $SIBIRC->getCategoryName($row['item_category_id'])."</td>
                                    <td>". $SIBIRC->getItemName($row['item_id'])."</td>
                                </tr>";
                           }
                        }
                        ?>
                  {{-- @foreach ($data as $row)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $SIBIRC->getCategoryName($row['item_category_id']) }}</td>
                        <td>{{ $SIBIRC->getItemName($row['item_id']) }}</td>
                    </tr>
                  @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-danger" href="{{ route('print-sales-invoice-by-item-not-sold-report') }}"> Preview</a>
            <a class="btn btn-primary" href="{{ route('export-sales-invoice-by-item-not-sold-report') }}"><i class="fa fa-download"></i> Export Data</a>
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