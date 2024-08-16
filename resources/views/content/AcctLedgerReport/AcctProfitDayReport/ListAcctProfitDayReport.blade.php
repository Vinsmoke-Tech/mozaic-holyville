@inject('ADR','App\Http\Controllers\AcctProfitDayReportController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Laba - Rugi</li>
    </ol>
  </nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Laporan Laba - Rugi</b>
</h3>
<br/>
<div id="accordion">
    <form action="{{ route('filter-profit-day-report') }}" method="post">
        @csrf
        <div class="card border border-dark">
            <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <h5 class="mb-0">
                    Filter
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Awal
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $start_date }}"/>
                            </div>
                        </div>

                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Akhir
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $end_date }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <a href="{{ route('reset-filter-profit-loss-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
        <div class="table-responsive pt-5">
            <table id="" style="width:100%" class="table table-bordered table-full-width">
                <thead>
                    <tr>
                        <td colspan='3' style='text-align:center;'>
                            <div style='font-weight:bold'>Laporan LABA - RUGI
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:center;'>
                            <div>
                                Period {{ date('d-m-Y', strtotime($start_date)) }} s.d. {{ date('d-m-Y', strtotime($end_date)) }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='3'></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='width: 25%'>
                            <table class="table table-bordered table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th style='text-align:center; width: 5%'>No</th>
                                        <th style='text-align:center; width: 10%'>Tanggal</th>
                                        <th style='text-align:center; width: 15%'>Keterangan</th>
                                        <th style='text-align:center; width: 10%'>Jumlah</th>
                                    </tr>
                                    <?php $no=1; ?>
                                    @foreach ($data as $row)
                                        @if($row['account_id'] == 28)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $row['journal_voucher_date'] }}</td>
                                                <td>Pendapatan Menu</td>
                                                <td>{{  $row['journal_voucher_amount'] }}</td>
                                            </tr>
                                        @elseif ($row['account_id'] == 52)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $row['journal_voucher_date'] }}</td>
                                                <td>Pendapatan Konsinyasi</td>
                                                <td>{{  $row['journal_voucher_amount'] }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $row['journal_voucher_date'] }}</td>
                                                <td>Pendapatan Lain Lain</td>
                                                <td>{{  $row['journal_voucher_amount'] }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                            
                                </thead>
                            </table>
                        </td>
                        <td style='width: 25%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                    <th style='text-align:center; width: 5%'>No</th>
                                    <th style='text-align:center; width: 10%'>Tanggal</th>
                                    <th style='text-align:center; width: 15%'>Keterangan</th>
                                    <th style='text-align:center; width: 10%'>Jumlah</th>
                                </tr>
                                <?php $no=1; ?>
                                @foreach ($data3 as $row)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $row['journal_voucher_date'] }}</td>
                                    <td>Kas Kecil</td>
                                    <td>{{  $row['journal_voucher_amount'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                        <td style='width: 25%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                    <th style='text-align:center; width: 5%'>No</th>
                                    <th style='text-align:center; width: 10%'>Tanggal</th>
                                    <th style='text-align:center; width: 15%'>Keterangan</th>
                                    <th style='text-align:center; width: 10%'>Jumlah</th>
                                </tr>
                                <?php $no=1; ?>
                                @foreach ($data2 as $row)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $row['journal_voucher_date'] }}</td>
                                    <td>{{ $row['journal_voucher_description'] }}</td>
                                    <td>{{  $row['journal_voucher_amount'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style='width: 25%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                    @foreach ($data as $row)
                                    <tr>
                                    </tr>
                                    @endforeach
                                </tr>
                            </table>
                        </td>

                        <td style='width: 25%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                
                                </tr>
                            </table>
                        </td>

                        <td style='width: 25%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-muted mt-3">
            <div class="form-actions float-right">
                <a class="btn btn-secondary" href="{{ url('balance-sheet-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
                <a class="btn btn-dark" href="{{ url('/profit-day-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
            </div>
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