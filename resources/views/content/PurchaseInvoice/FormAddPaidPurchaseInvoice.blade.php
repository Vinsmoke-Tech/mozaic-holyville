@inject('PurchaseInvoice','App\Http\Controllers\PurchaseInvoiceController' )
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function totalAll(){
        var ttl = 0;
        var profit = 0;
        var unitprice = 0;
        $(".input-price").each(function (index, element) {
            var id= $(this).data('id');
            var qty = $('#item_sold_'+id).val()||0;
            var cost = parseInt($('#item_unit_cost_'+id).val()||0);
            var quantity = $('#quantity_'+id).val()||0;
            notsold =  ((quantity||0)-qty);
            ttl += ($(this).val()||0)*qty;
            profit += ((($(this).val()||0)-cost)*qty);
            unitprice += ((qty||0)*cost);

        });
        $("#remaining_item_sold").val(notsold);
        $("#remaining_subtotal_amount").val(ttl);
        $("#total").val(profit);
        $("#remaining_unit_price").val(unitprice);
        
        return ttl;
    }
      $(document).ready(function(){
        // $("#item_sold").change(function(){
        //     var quantity = $("#quantity").val();
        //     var cost = $("#item_sold").val();
        //     var item_cost = $("#item_unit_cost").val();
        //     var item_price = $("#item_unit_price").val();
        //     var subtotal_amount = $("#subtotal_amount").val();
        //     var margin = $("#margin").val();
        //     var total = $("#total").val();
        //     var remaining = $("#remaining_subtotal_amount").val(); 
        //     var remaining_unit_price = $("#remaining_unit_price").val(); 
        //     var grandtotal = $("#grandtotal").val(); 

        //     var subtotalqty = quantity - cost;
        //     var subtotal =  cost * item_price;
        //     var subtotal2 =  cost * item_cost;
        //     var totalmargin = (item_price - item_cost)*cost;
        //     var total =  totalmargin;

        //     $("#remaining_item_sold").val(subtotalqty);
        //     $("#remaining_subtotal_amount").val(subtotal);
        //     $("#remaining_unit_price").val(subtotal2);
        //     $("#total").val(Math.round(totalmargin));
        //     $("#grandtotal").val(Math.round(total));


        // });

        $("#margin").change(function(){
            var quantity = $("#quantity").val();
            var cost = $("#item_sold").val();
            var not = $("#item_not_sold").val();
            var item_cost = $("#item_unit_cost").val();
            var item_price = $("#item_unit_price").val();
            var subtotal_amount = $("#subtotal_amount").val();
            var margin = $("#margin").val();
            var total = $("#total").val();
            var remaining = $("#remaining_subtotal_amount").val(); 
            var remaining_unit_price = $("#remaining_unit_price").val(); 
            var grandtotal = $("#grandtotal").val(); 

            var subtotalqty = quantity - cost;
            var subtotal =  cost * item_price;
            var subtotal2 =  cost * item_cost;
            var totalmargin = (item_price - item_cost)*cost;

            var total = totalmargin;

            $("#remaining_item_sold").val(subtotalqty);
            $("#remaining_subtotal_amount").val(Math.round(subtotal));
            $("#remaining_unit_price").val(subtotal2);
            $("#total").val(Math.round(totalmargin));
            $("#grandtotal").val(Math.round(total));

        });
        
    });

</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('purchase-invoice') }}">Daftar Penjualan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Penjualan</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Pembayaran
</h3>
<br/>

<form action="{{ route('add-paid-purchase-invoice') }}" method="post" id="form-prevent">
    @csrf
<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
        {{-- <div class="float-right">
            <button onclick="location.href='{{ url('purchase-invoice') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div> --}}
    </div>


    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">No. Invoice Penjualan<a class='red'> *</a></a>
                    <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $purchaseinvoice['purchase_invoice_no'] ?? ''}}" readonly/> 
                    <input class="form-control input-bb" name="purchase_invoice_id" id="purchase_invoice_id" type="text" autocomplete="off" value="{{ $purchaseinvoice['purchase_invoice_id'] ?? ''}}" hidden/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Tanggal Invoice Penjualan<a class='red'> *</a></a>
                    <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off"  value="{{ date('d-m-Y', strtotime($purchaseinvoice['purchase_invoice_date'])) }}" readonly/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Metode Pembayaran<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" autocomplete="off" value="{{ $purchase_method_list[$purchaseinvoice['purchase_method']] }}" readonly/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Tanggal Pelunasan<a class='red'> *</a></a>
                    <input class="form-control input-bb" name="journal_voucher_date" id="journal_voucher_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ date('d-m-Y', strtotime($purchaseinvoice['purchase_invoice_date'])) }}" />
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
        <div class="form-body form">
            <div class="table-responsive">
                <table class="table table-bordered table-advance table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style='text-align:center'>No</th>
                            <th style='text-align:center'>Nama Barang</th>
                            <th style='text-align:center'>Qty Pembelian</th>
                            <th style='text-align:center'>Stok Sistem</th>
                            <th style='text-align:center'>Harga Beli</th>
                            <th style='text-align:center'>Harga Jual</th>
                            <th style='text-align:center'>Barang Terjual</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                            @foreach ($purchaseinvoiceitem as $purchaseinvoiceitem )
                                <tr>
                                    <td class="text-center">{{ $no++ }}.</td>
                                    <td>{{ $PurchaseInvoice->getItemName($purchaseinvoiceitem['item_id']) }}</td>
                                    <td style="text-align: right">{{ $purchaseinvoiceitem['quantity'] }}</td>
                                <td style="text-align: right">{{ $PurchaseInvoice->getStock($purchaseinvoiceitem['item_id']) }}</td>
                                    <td style="text-align: right"> {{ number_format($purchaseinvoiceitem['item_unit_cost'],2,'.',',') }} </td>
                                    <td style="text-align: right"> <input onchange="totalAll();" type="text" style="text-align  : right !important;" class="form-control input-bb input-price" data-id="{{$purchaseinvoiceitem->purchase_invoice_item_id}}" name="item[{{ $no }}][item_unit_price]" id="item_unit_price_{{$purchaseinvoiceitem->purchase_invoice_item_id}}" value="{{ $PurchaseInvoice->getLastBalance($purchaseinvoiceitem['item_id'])}}" autocomplete="off" /></td>
                                    <td style="text-align: right"> <input onchange="totalAll();" type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][item_sold]" id="item_sold_{{$purchaseinvoiceitem->purchase_invoice_item_id}}" value="" autocomplete="off"/> </td>
                                </tr>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][item_id]" id="item_id_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['item_id'] }}" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][item_category_id]" id="item_category_id_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['item_category_id'] }}" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][item_unit_id]" id="item_unit_id_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['item_unit_id'] }}" autocomplete="off" hidden/>


                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][quantity]" id="quantity_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['quantity'] }}" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][item_unit_cost]" id="item_unit_cost_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['item_unit_cost']}}" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][subtotal_amount]" id="subtotal_amount_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['subtotal_amount'] }}" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][remaining_item_sold]" id="remaining_item_sold_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][id]" id="purchase_invoice_id_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}" value="{{ $purchaseinvoiceitem['purchase_invoice_item_id']}}" autocomplete="off" hidden/>
                                <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item[{{ $no }}][remaining_subtotal_amount]" id="remaining_subtotal_amount_{{ $purchaseinvoiceitem->purchase_invoice_item_id }}"value="{{ $purchaseinvoiceitem['remaining_subtotal_amount']}}" autocomplete="off" hidden/>
                                
                            @endforeach
                            <tr>
                                <td colspan="5">Total Harga Beli</td>
                                <td style="text-align: right ">{{ number_format($purchaseinvoiceitem['subtotal_amount'],2,'.',',') }}</td>
                            </tr>
                            {{-- <tr> --}}
                                {{-- <td colspan="5">Barang Terjual</td> --}}
                                {{-- <td style='text-align  : right !important; width: 500px;'> --}}
                                    {{-- <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="item_sold" id="item_sold" value="" autocomplete="off"/> --}}
                                  
                                {{-- </td> --}}
                            {{-- </tr> --}}
                            <tr>
                                <td colspan="5">Total Barang Terjual</td>
                                <td style="text-align: right ">
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="remaining_item_sold" id="remaining_item_sold" value="" autocomplete="off" hidden/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="remaining_subtotal_amount" id="remaining_subtotal_amount" value="" autocomplete="off" readonly/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="remaining_unit_price" id="remaining_unit_price" value="" autocomplete="off" hidden/>


                                </td>
                            </tr>
                            {{-- <tr>
                                <td colspan="5">Margin Keuntungan</td>
                                <td style="text-align: right ">
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="margin" id="margin" value="" autocomplete="off"/>
                                    
                                </td>
                            </tr> --}}
                            <tr>
                                <td colspan="5"> Keuntungan Diambil</td>
                                <td style="text-align: right ">
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="total" id="total" value="" autocomplete="off" readonly/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="grandtotal" id="grandtotal" value="" autocomplete="off" hidden/>

                                    
                                </td>
                            </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fa fa-credit-card"></i> &nbsp; Bayar</button>
            </div>
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