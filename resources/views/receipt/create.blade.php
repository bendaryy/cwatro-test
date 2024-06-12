@extends('layouts.main')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    function randomTest() {
        var internalValue = document.getElementById('receiptNumber');
        internalValue.value = Math.floor(Math.random() * 105140);
    }
</script>


<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
<title>{{ config('app.name', 'E_TAX') }}</title>



<style>
    th,
    td,
    tr,
    table {
        padding: 5px;
        text-align: center;
    }

    .borderNone {
        border: none
    }

    .borderNone:focus {
        outline: none;
    }

    .online-actions {
        display: none;
    }

    .navbar-expand-sm {
        justify-content: center
    }

    {{-- input[type="number"] {
        width: 130;
        text-align: center
    } --}} {{-- input[name="totalItemsDiscount[]"],
    input[name="totalAmount2"],
    input[name="totalAmount"] {
        width: 250;
    } --}} input[readonly] {
        background-color: #ccc
    }

    hr {
        border: 4px solid orange;
    }
</style>





@section('content')
    <div class=" page-content page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        </hr>
        <div class="breadcrumb-title pe-3">الإيصالات</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">إضافة إيصال</li>
                </ol>
            </nav>
        </div>

    </div>
    <div class="wrapper" style="background-color: white">




        <div class="card text-center" style="margin: auto;margin-bottom: 50px">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label">إختر إسم الشركة / الشخص</label>
                        <select class="single-select" name="receiverName" class="form-control" id="receiverName">
                            <option selected disabled>إختر إسم الشركة / الشخص</option>
                            @foreach ($allCompanies as $companies)
                                <option value="{{ $companies->id }}" class="form-control">
                                    {{ $companies->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>
                    <div class="col-2" style="margin-top: 23">

                        <a href="{{ route('customer.create') }}" class="btn btn btn-success "
                            style="text-align: center;min-width: 250px!important; background-color: #1598ca;
                                                    border-color: #1598ca;">
                            @lang('site.addReceiver')
                        </a>
                    </div>
                </div>
            </div>


            {{-- <div style="text-align: center">
                <a href="{{ route('createInvoice') }}" class="btn btn-success" style="text-align: center;min-width: 250px!important;background-color: #1598ca;
                                    border-color: #1598ca; margin-bottom: 30px;">@lang('site.backtochoose')</a>
            </div> --}}




            <div style="margin-bottom: 50px">
                <form method="POST" action="{{ route('sendReceipt') }}">
                    @method('POST')
                    @csrf

                    <div class="row justify-content-center">



                        <div class="col-xl-10 invoice-address-client">

                            <div class="row">

                                <div class="col-6">
                                    <label for="payment-method-country" class="form-label">@lang('site.Receiver_type')</label>
                                    <div>
                                        <select name="receiverType" class="form-control text-center form-select">
                                            <option value="P" style="font-size: 20px">@lang('site.Person')</option>
                                            <option value="B" style="font-size: 20px">@lang('site.Company')</option>
                                            <option value="F" style="font-size: 20px">@lang('site.Forign')</option>

                                        </select>
                                    </div>
                                </div>


                                <div class="col-6">
                                    <label class="form-label">@lang('site.Receiver_to')</label>
                                    <div class="">
                                        <input type="text" id="getReceiverName" class="form-control text-center"
                                            name="receiverName" placeholder="@lang('site.Receiver_to')">
                                    </div>
                                </div>
                                <div class="invoice-address-client-fields">

                                    <div class="row">

                                        <div class="col-6">
                                            <label class="form-label">@lang('site.Reciever_Registration_Number_ID') (إختيارى اذا كانت القيمة النهائية
                                                اقل من 150 الف)</label>
                                            <div class="">
                                                <input type="number" class="form-control text-center" name="receiverId"
                                                    id="getReceiverId" placeholder="@lang('site.Reciever_Registration_Number_ID')">
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <label class="form-label"> رقم الموبايل (إختيارى)</label>
                                            <div class="">
                                                <input type="number" class="form-control  text-center" id="mobileNumber"
                                                    name="mobileNumber" placeholder="رقم الموبايل">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <label class="form-label">رقم الدفع (إختيارى)</label>
                                            <div class="">
                                                <input type="text" class="form-control text-center" id="getpaymentNumber"
                                                    name="paymentNumber" placeholder="رقم الدفع">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row">

                                    <div class="col-6">
                                        <label class="form-label">رقم الدفع (إختيارى)</label>
                                        <div class="">
                                            <input type="text" class="form-control text-center" id="getpaymentNumber" name="paymentNumber"
                                                placeholder="رقم الدفع">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label">@lang('site.StreetName') </label>
                                        <div class="">
                                            <input type="text" class="form-control text-center" id="getStreet" name="street"
                                                placeholder="@lang('site.StreetName')">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">


                                    <div class="col-6">
                                        <label class="form-label">@lang('site.Building_Name_No')</label>
                                        <div class="">
                                            <input type="text" class="form-control  text-center"
                                                name="receiverBuildingNumber" id="getBuildingNumber" placeholder="@lang('site.Building_Name_No')">
                                        </div>
                                    </div>


                                    <div class="col-6">
                                        <label class="form-label"> @lang('site.PostalCode')</label>
                                        <div class="">
                                            <input type="text" id="getPostalCode" class="form-control text-center" name="receiverPostalCode"
                                                placeholder="@lang('site.PostalCode')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label"> @lang('site.FloorNo')</label>
                                        <div class="">
                                            <input type="text" class="form-control text-center" id="getFloor" name="receiverFloor"
                                                placeholder="  @lang('site.FloorNo')">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label">@lang('site.FlatNo')</label>
                                        <div class="">
                                            <input type="text" class="form-control text-center" id="getRoom" name="receiverRoom"
                                                placeholder="@lang('site.FloorNo')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label"> @lang('site.landmark')</label>
                                        <div class="">
                                            <input type="text" class="form-control  text-center" id="getLandMark" name="receiverLandmark"
                                                placeholder="@lang('site.landmark')">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label"> @lang('site.AdditionalInformation')</label>
                                        <div class="">
                                            <input type="text" class="form-control text-center"
                                                name="receiverAdditionalInformation"
                                                id="getAdditional"
                                                placeholder="@lang('site.AdditionalInformation')">

                                        </div>
                                    </div>
                                </div> --}}


                                    <div class="row">
                                        <div class="col-12">
                                            <label for="payment-method-country" class="form-label">
                                                كود النشاط</label>
                                            <div class="">
                                                <select name="taxpayerActivityCode" class="form-select text-center">

                                                    @foreach ($ActivityCodes as $code)
                                                        <option value="{{ $code->code }}"> {{ $code->Desc_ar }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{-- <div class="col-6">
                                        <label for="payment-method-country" class="form-label">@lang('site.Document
                                            Type')
                                        </label>
                                        @livewire('type')
                                    </div> --}}

                                    </div>






                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">تاريخ الإيصال</label>
                                            <div class="">
                                                <input type="date" value="{{ date('Y-m-d') }}"
                                                    class="form-control text-center" name="date" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">رقم الإيصال</label>

                                            <input type="text" class="form-control text-center" id="receiptNumber"
                                                name="receiptNumber" placeholder="رقم الإيصال">


                                        </div>
                                        <div class="col-2" style="margin-top: 27px">


                                            <button onClick="randomTest();" class="btn btn-info"
                                                type="button">@lang('site.Generate')</button>

                                        </div>


                                    </div>





                                    <hr>









                                </div>

                            </div>


                        </div>
                        <hr style="border: 1px solid white;margin:50px 20px">
                        <div class="form-body mt-4">
                            <div class="row">
                                <div class="col-lg-8" style="margin-top: -120px;">

                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="lineDetails">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne" aria-expanded="true"
                                                    aria-controls="collapseOne"> @lang('site.Line Items')</button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show"
                                                aria-labelledby="lineDetails" data-bs-parent="#accordionExample">
                                                <div class="accordion-body" id="newOne">

                                                    <div class="border border-3 p-4 rounded">
                                                        <div class="mb-3">
                                                            <label for="inputProductTitle"
                                                                class="form-label">@lang('site.Line Item')</label>
                                                            <select name="itemCode[]" id="itemCode"
                                                                class="form-control form-control-sm">
                                                                <option value="10001699" selected>أقمشة / منسوجات التطريز
                                                                </option>

                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="inputProductDescription"
                                                                class="form-label">@lang('site.Line Decription')</label>
                                                            <textarea name="invoiceDescription[]" class="form-control" id="inputProductDescription" rows="2"></textarea>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="linePrice"
                                                                    class="form-label">@lang('site.price')</label>
                                                                <input class="form-control" step="any" type="number"
                                                                    step="any" name="amountEGP[]" id="amountEGP"
                                                                    onkeyup="operation(this.value),findTotalSalesAmount();;"
                                                                    onmouseover="operation(this.value),findTotalSalesAmount();;">
                                                            </div>
                                                            <div class=" col-md-6">
                                                                <label class="form-label">@lang('site.quantity')</label>
                                                                <input class="form-control" type="number" step="any"
                                                                    name="quantity[]" id="quantity"
                                                                    onkeyup="proccess(this.value),findTotalSalesAmount();"
                                                                    onmouseover="proccess(this.value),findTotalSalesAmount();">
                                                            </div>
                                                        </div>
                                                        <div class=" row g-3">
                                                            <div class="col-md-6">
                                                                <label for="inputProductTitle"
                                                                    class="form-label">@lang('site.Tax added Type')</label>

                                                                <select name="t1subtype[]" required id="t1subtype"
                                                                    class="form-control form-control-sm single-select">

                                                                    @foreach ($taxTypes as $type)
                                                                        @if ($type->parent === 'T1')
                                                                            <option value="{{ $type->code }}"
                                                                                style="font-size: 15px;width: 100px;">
                                                                                {{ $type->name_ar }}
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="lineTaxAdd"
                                                                    class="form-label">@lang('site.Tax_added')</label>
                                                                <input type="number" class="form-control" name="rate[]"
                                                                    id="rate" class="form-control form-control-sm"
                                                                    onkeyup="findTotalt2Amount()"
                                                                    onmouseover="findTotalt2Amount()"
                                                                    placeholder="@lang("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            site.Tax_added")">
                                                            </div>
                                                        </div>
                                                        <div class="row g-3" style="display: none">
                                                            <div class="col-md-6">
                                                                <label for="inputProductTitle"
                                                                    class="form-label">@lang('site.Tax t4 Type')</label>
                                                                <select name="t4subtype[]" required id="t4subtype"
                                                                    class="form-control form-control-sm single-select">
                                                                    @foreach ($taxTypes as $type)
                                                                        @if ($type->parent === 'T4')
                                                                            <option value="{{ $type->code }}"
                                                                                style="font-size: 15px;width: 100px;">
                                                                                {{ $type->name_ar }}
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="lineTaxT4"
                                                                    class="form-label">@lang("site.Tax
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            t4 Value")</label>
                                                                <input type="number" class="form-control"
                                                                    name="t4rate[]" id="t4rate"
                                                                    onkeyup="findTotalt4Amount()"
                                                                    onmouseover="findTotalt4Amount()"
                                                                    placeholder="@lang("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            site.Tax t4 Value")">
                                                            </div>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="lineDiscount" class="form-label">الخصم
                                                                    التجارى</label>

                                                                <input class="form-control"
                                                                    placeholder=" @lang('site.Discount')" type="number"
                                                                    value="0" step="any" name="discountAmount[]"
                                                                    id="discountAmount"
                                                                    onkeyup="discount(this.value),findTotalDiscountAmount(),findTotalNetAmount(),findTotalt4Amount(),findTotalt2Amount()"
                                                                    onmouseover="discount(this.value),findTotalDiscountAmount(),findTotalNetAmount(),findTotalt4Amount(),findTotalt2Amount()">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="lineDiscountAfterTax" class="form-label">خصم
                                                                    الأصناف
                                                                </label>
                                                                <input type="number" class="form-control" step="any"
                                                                    name="itemsDiscount[]" id="itemsDiscount"
                                                                    onkeyup="itemsDiscountValue(this.value),findTotalAmount(),findTotalItemsDiscountAmount()"
                                                                    onmouseover="itemsDiscountValue(this.value),findTotalAmount(),findTotalItemsDiscountAmount()"
                                                                    placeholder="@lang(' site.Discount_After_Tax')">
                                                            </div>
                                                        </div>
                                                    </div></BR>
                                                    <div class="border border-3 p-4 rounded">
                                                        <div class="mb-3 text-center">
                                                            <div style="margin: 10px"> @lang('site.Line Total') </div>
                                                            <div class="row g-3">
                                                                <div class="col-md-12">
                                                                    <label for="TotalTaxableFees"
                                                                        class="form-label">اجمالى
                                                                        ضريبة القيمة المضافة</label>
                                                                    <input type="number" readonly class="form-control"
                                                                        step="any" name="t2Amount[]" id="t2"
                                                                        onkeyup="findTotalt2Amount()"
                                                                        onmouseover="findTotalt2Amount()"
                                                                        placeholder="@lang("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            site.Total Taxable Fees")">
                                                                </div>
                                                                <div class="col-md-6" style="display: none">
                                                                    <label for="Totalt4Amount" class="form-label">اجمالى
                                                                        ضريبة المنبع</label>
                                                                    <input type="number" class="form-control"
                                                                        name="t4Amount[]" readonly id="t4Amount"
                                                                        onkeyup="findTotalt4Amount()"
                                                                        onmouseover="findTotalt4Amount()"
                                                                        placeholder="@lang("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            site.Total T4 Amount")">
                                                                </div>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label for="Subtotal"
                                                                        class="form-label">@lang('site.Sub total')</label>
                                                                    <input type="number" class="form-control"
                                                                        name="salesTotal[]" readonly id="salesTotal"
                                                                        placeholder="@lang(' site.Sub total')">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="NetTotal"
                                                                        class="form-label">@lang('site.Net Total')</label>
                                                                    <input type="number" class="form-control" readonly
                                                                        name="netTotal[]" id="netTotal"
                                                                        onkeyup="nettotal(this.value),findTotalNetAmount()"
                                                                        onmouseover="nettotal(this.value),findTotalNetAmount()"
                                                                        placeholder="@lang(' site.Net Total')">
                                                                </div>
                                                            </div>
                                                            <div class="row g-3">

                                                                <div class="col-md-12">
                                                                    <label for="lineTotal"
                                                                        class="form-label">@lang('site.lineTotal')</label>
                                                                    <input type="number" class="form-control"
                                                                        name="totalItemsDiscount[]" readonly
                                                                        id="totalItemsDiscount"
                                                                        onkeyup="findTotalAmount()"
                                                                        onmouseover="findTotalAmount()"
                                                                        placeholder="@lang("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            site.lineTotal")">
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="z-index:1;text-align: center">
                                                    <button id="addNewOne" type="button" class="btn btn-info"
                                                        style="width: 200px">@lang('site.add_name')</button>

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>







                                <div class="col-lg-4" style="margin-top: -120px">
                                    <div class="border border-3 p-4 rounded">

                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label for="findTotalt2Amount" class="form-label">إجمالى ضريبة القيمة
                                                    المضافة</label>
                                                <input type="number" class="form-control" step="any"
                                                    name="totalt2Amount" onmouseover="findTotalt2Amount()"
                                                    onkeyup="findTotalt2Amount()" readonly id="totalt2Amount">
                                            </div>
                                            <div class="col-md-6" style="display: none">
                                                <label for="findTotalt4Amount" class="form-label">إجمالى ضريبة
                                                    المنبع</label>
                                                <input class="form-control" type="number" step="any"
                                                    name="totalt4Amount" onmouseover="findTotalt4Amount()"
                                                    onkeyup="findTotalt4Amount()" readonly id="totalt4Amount">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="salesTotal" class="form-label">إجمالى الخصم التجارى</label>
                                                <input type="number" class="form-control" name="totalDiscountAmount"
                                                    onmouseover="findTotalDiscountAmount()"
                                                    onkeyup="findTotalDiscountAmount()" readonly id="totalDiscountAmount">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="netTotal" class="form-label">الإجمالى</label>
                                                <input type="number" class="form-control" step="any"
                                                    name="TotalSalesAmount" onmouseover="findTotalSalesAmount()"
                                                    onkeyup="findTotalSalesAmount()" readonly id="TotalSalesAmount">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="findTotalNetAmount" class="form-label">إجمالى المبلغ
                                                    الصافى</label>
                                                <input type="number" step="any" class="form-control"
                                                    name="TotalNetAmount" onmouseover="findTotalNetAmount()"
                                                    onkeyup="findTotalNetAmount()" readonly id="TotalNetAmount">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="TotalDiscount" class="form-label">إجمالى خصم الأصناف</label>
                                                <input type="number" step="any" name="totalItemsDiscountAmount"
                                                    class="form-control" onmouseover="findTotalItemsDiscountAmount()"
                                                    onkeyup="findTotalItemsDiscountAmount()" readonly
                                                    id="totalItemsDiscountAmount">
                                            </div>


                                            <div class="col-12">
                                                <label for="ExtraInvoiceDiscount" class="form-label">الخصم الإضافى (مابعد
                                                    الضريبة) </label>
                                                <input type="number" class="form-control" step="any"
                                                    name="ExtraDiscount" id="ExtraDiscount"
                                                    onkeyup="Extradiscount(this.value),findTotalAmount()"
                                                    onmouseover="Extradiscount(this.value),findTotalAmount()" required>
                                            </div>


                                            <div class="col-12">
                                                <label for="findTotalAmount" class="form-label">الإجمالى قبل الخصم
                                                </label>
                                                <input class="form-control" type="number" step="any"
                                                    name="totalAmount" readonly id="totalAmount">
                                            </div>


                                            <div class="col-12">
                                                <label for="findTotalAmount" class="form-label">الإجمالى بعد الخصم
                                                </label>
                                                <input type="number" class="form-control"
                                                    style="color: red;font-weight: bold;font-size: 20px" type="number"
                                                    step="any" name="totalAmount2" readonly id="totalAmount2">
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">إرسال الإيصال</button>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        @endsection







        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                let i = 1
                $("#addNewOne").click(function() {
                    i++;
                    $('#newOne').append(
                        `<div id="row${i}">
                                <button type="button" style="margin-right:30px" name="remove" id="${i}"  class="btn btn-danger btn_remove">x</button>
                               <div class="border border-3 p-4 rounded">
                             <div class="mb-3">
                                <label for="inputProductTitle"
                                    class="form-label">@lang('site.Line Item')</label>
                                <select name="itemCode[]" id="itemCode" class="form-control form-control-sm">
                             <option value="10001699" selected>أقمشة / منسوجات التطريز</option>
                                 </select>
                             </div>
                                <div class="mb-3">
                                    <label for="inputProductDescription" class="form-label">@lang('site.Line Decription') ${i}</label>
                                    <textarea name="invoiceDescription[]" class="form-control" id="inputProductDescription" rows="2"></textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="linePrice" class="form-label">@lang('site.price')</label>
                                        <input class="form-control" step="any" type="number" step="any" name="amountEGP[]" id="amountEGP${i}"
                                            onkeyup="operation${i}(this.value),findTotalSalesAmount();;"
                                            onmouseover="operation${i}(this.value),findTotalSalesAmount();;">
                                    </div>
                                    <div class=" col-md-6">
                                        <label class="form-label">@lang('site.quantity')</label>
                                        <input class="form-control" type="number" step="any" name="quantity[]" id="quantity${i}"
                                            onkeyup="proccess${i}(this.value),findTotalSalesAmount();"
                                            onmouseover="proccess${i}(this.value),findTotalSalesAmount();">
                                    </div>
                                </div>
                                <div class=" row g-3">
                                    <div class="col-md-6">
                                        <label for="inputProductTitle" class="form-label">@lang('site.Tax added Type')</label>
                                        <select name="t1subtype[]" required id="t1subtype" class="form-control form-control-sm single-select">
                                            @foreach ($taxTypes as $type)
                                                @if ($type->parent === 'T1')
                                                    <option value="{{ $type->code }}" style="font-size: 15px;width: 100px;">
                                                        {{ $type->name_ar }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lineTaxAdd" class="form-label">@lang('site.Tax_added')</label>
                                        <input type="number" class="form-control" name="rate[]" id="rate${i}" class="form-control form-control-sm"
                                            onkeyup="findTotalt2Amount()" onmouseover="findTotalt2Amount()" placeholder="@lang('site.Tax_added')">
                                    </div>
                                </div>
                                <div class="row g-3" style="display:none">
                                    <div class="col-md-6">
                                        <label for="inputProductTitle" class="form-label">@lang('site.Tax t4 Type')</label>
                                        <select name="t4subtype[]" required id="t4subtype" class="form-control form-control-sm single-select">

                                            @foreach ($taxTypes as $type)
                                                @if ($type->parent === 'T4')
                                                    <option value="{{ $type->code }}" style="font-size: 15px;width: 100px;">
                                                        {{ $type->name_ar }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lineTaxT4" class="form-label">@lang('site.Tax t4 Value')</label>
                                        <input type="number" class="form-control" name="t4rate[]" id="t4rate${i}" onkeyup="findTotalt4Amount()"
                                            onmouseover="findTotalt4Amount()" placeholder="@lang('site.Tax t4 Value')">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="lineDiscount" class="form-label">@lang('site.Discount')</label>
                                        <input class="form-control" value="0" placeholder=" @lang('site.Discount')" type="number" step="any"
                                            name="discountAmount[]" id="discountAmount${i}"
                                            onkeyup="discount${i}(this.value),findTotalDiscountAmount(),findTotalNetAmount(),findTotalt4Amount(),findTotalt2Amount()"
                                            onmouseover="discount${i}(this.value),findTotalDiscountAmount(),findTotalNetAmount(),findTotalt4Amount(),findTotalt2Amount()">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lineDiscountAfterTax" class="form-label">@lang('site.Discount_After_Tax') </label>
                                        <input type="number" class="form-control" step="any" name="itemsDiscount[]" id="itemsDiscount${i}"
                                            onkeyup="itemsDiscountValue${i}(this.value),findTotalAmount(),findTotalItemsDiscountAmount()"
                                            onmouseover="itemsDiscountValue${i}(this.value),findTotalAmount(),findTotalItemsDiscountAmount()"
                                            placeholder="@lang('site.Discount_After_Tax')">
                                    </div>
                                </div>
                            </div></BR>
                            <div class="border border-3 p-4 rounded">
                                <div class="mb-3">
                                    @lang('site.Line Total')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="TotalTaxableFees" class="form-label">إجمالى ضريبة القيمة المضافة</label>
                                            <input type="number" readonly class="form-control" step="any" name="t2Amount[]" id="t2${i}"
                                                onkeyup="findTotalt2Amount()" onmouseover="findTotalt2Amount()" placeholder="@lang(" site.Total
                                                                                                                                                                                                                                                                                                                                                                                                Taxable Fees")">
                                        </div>
                                        <div class="col-md-6" style="display:none">
                                            <label for="Totalt4Amount" class="form-label">@lang('site.Totalt4Amount')</label>
                                            <input type="number" class="form-control" name="t4Amount[]" readonly id="t4Amount${i}"
                                                onkeyup="findTotalt4Amount()" onmouseover="findTotalt4Amount()" placeholder="@lang('site.Totalt4Amount')">
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="Subtotal" class="form-label">@lang('site.Sub total')</label>
                                            <input type="number" class="form-control" name="salesTotal[]" readonly id="salesTotal${i}"
                                                placeholder="@lang('site.Sub total')">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="NetTotal" class="form-label">@lang('site.Net Total')</label>
                                            <input type="number" class="form-control" readonly name="netTotal[]" id="netTotal${i}"
                                                onkeyup="nettotal${i}(this.value),findTotalNetAmount()"
                                                onmouseover="nettotal${i}(this.value),findTotalNetAmount()" placeholder="@lang('site.Net Total')">
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="lineTotal" class="form-label">@lang('site.lineTotal')</label>
                                            <input type="number" class="form-control" name="totalItemsDiscount[]" readonly id="totalItemsDiscount${i}"
                                                onkeyup="findTotalAmount()" onmouseover="findTotalAmount()" placeholder="@lang('site.lineTotal')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div> `
                    );
                    $('.single-select').select2();
                    $('<script> function operation' + i +
                        '(value) {var x, y, z;  var quantity = document.getElementById("quantity' + i +
                        '").value; x = value * quantity; document.getElementById("salesTotal' + i +
                        '").value = x.toFixed(5);};  function proccess' + i +
                        '(value) {var x, y, z;  var amounEGP = document.getElementById("amountEGP' + i +
                        '").value; y = value * amounEGP; document.getElementById("salesTotal' + i +
                        '").value = y.toFixed(5);};function discount' + i +
                        '(value) {var salesTotal, netTotal, z, t2valueEnd, t1Value, rate, t4rate, t4Amount; salesTotal = document.getElementById("salesTotal' +
                        i +
                        '").value; netTotal = salesTotal - value; netTotalEnd = document.getElementById("netTotal' +
                        i + '").value = netTotal.toFixed(5); rate = document.getElementById("rate' + i +
                        '").value; t4rate = document.getElementById("t4rate' + i +
                        '").value;  t2valueEnd = document.getElementById("t2' + i +
                        '").value = ((netTotalEnd * rate) / 100).toFixed(5); t4Amount = document.getElementById("t4Amount' +
                        i +
                        '").value = ((netTotal * t4rate) / 100).toFixed(5);}; function itemsDiscountValue' +
                        i +
                        '(value) {var x, netTotal, t1amount, t2amount, t4Amount;netTotal = document.getElementById("netTotal' +
                        i + '").value;t2amount = document.getElementById("t2' + i +
                        '").value;t4Amount = document.getElementById("t4Amount' + i +
                        '").value;x = parseFloat(netTotal) + parseFloat(t2amount) - parseFloat(t4Amount) - parseFloat(value);document.getElementById("totalItemsDiscount' +
                        i + '").value = x.toFixed(5);};  </' + 'script>').appendTo('#test123');
                    $(document).on('click', '.btn_remove', function() {
                        var button_id = $(this).attr("id");
                        $("#row" + button_id + "").remove()
                        findTotalDiscountAmount();
                        findTotalSalesAmount();
                        findTotalNetAmount();
                        findTotalt4Amount();
                        findTotalt2Amount();
                        findTotalAmount();
                        findTotalItemsDiscountAmount();
                    })
                });
            });
        </script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>




        <script id="test123">
            // this is invoice 1
            function operation(value) {
                var x, y, z;
                var quantity = document.getElementById("quantity").value;
                x = value * quantity;
                document.getElementById("salesTotal").value = x.toFixed(5);
            };

            function proccess(value) {
                var x, y, z;
                var amounEGP = document.getElementById("amountEGP").value;
                y = value * amounEGP;
                document.getElementById("salesTotal").value = y.toFixed(5);
            };

            function discount(value) {
                var salesTotal, netTotal, z, t2valueEnd, t1Value, rate, t4rate, t4Amount;
                salesTotal = document.getElementById("salesTotal").value;
                netTotal = salesTotal - value;
                netTotalEnd = document.getElementById("netTotal").value = netTotal.toFixed(5);
                rate = document.getElementById("rate").value;
                t4rate = document.getElementById("t4rate").value;
                t2valueEnd = document.getElementById("t2").value =
                    ((netTotalEnd * rate) / 100).toFixed(5);
                t4Amount = document.getElementById("t4Amount").value =
                    ((netTotal * t4rate) / 100).toFixed(5);
            }

            function itemsDiscountValue(value) {
                var x, netTotal, t1amount, t2amount, t4Amount;
                netTotal = document.getElementById("netTotal").value;
                t2amount = document.getElementById("t2").value;
                t4Amount = document.getElementById("t4Amount").value;
                x =
                    parseFloat(netTotal) +
                    parseFloat(t2amount) -
                    parseFloat(t4Amount) -
                    parseFloat(value);
                document.getElementById("totalItemsDiscount").value = x.toFixed(5);
            }

            function Extradiscount(value) {
                var totalDiscount, x;
                totalDiscount = document.getElementById("totalAmount").value;
                x = totalDiscount - value;
                document.getElementById("totalAmount2").value = x.toFixed(5);
            }

            function findTotalDiscountAmount() {
                var arr = document.getElementsByName("discountAmount[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("totalDiscountAmount").value = tot.toFixed(5);
            }

            function findTotalSalesAmount() {
                var arr = document.getElementsByName("salesTotal[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("TotalSalesAmount").value = tot.toFixed(5);
            }

            function findTotalNetAmount() {
                var arr = document.getElementsByName("netTotal[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("TotalNetAmount").value = tot.toFixed(5);
            }

            function findTotalt4Amount() {
                var arr = document.getElementsByName("t4Amount[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("totalt4Amount").value = tot.toFixed(5);
            }

            function findTotalt2Amount() {
                var arr = document.getElementsByName("t2Amount[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("totalt2Amount").value = tot.toFixed(5);
            }

            function findTotalAmount() {
                var arr = document.getElementsByName("totalItemsDiscount[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("totalAmount").value = tot.toFixed(5);
            }

            function findTotalItemsDiscountAmount() {
                var arr = document.getElementsByName("itemsDiscount[]");
                var tot = 0;
                for (var i = 0; i < arr.length; i++) {
                    if (parseFloat(arr[i].value)) {
                        tot += parseFloat(arr[i].value);
                    }
                }
                document.getElementById("totalItemsDiscountAmount").value = tot.toFixed(5);
                document.getElementById('inputProductDescription').value = 'test'
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        {{-- <script>
        $(document).ready(function () {
  $(document).on("change", "#selectId", function () {
    var select = $(this).val();
    $.ajax({
      url: route('getCompany',select),
      type: "GET",
      dataType: "json",
      // data: {
      //   select: select,
      // },
      success: function (data) {
        console.log(data);
        $("#code").val(data.data.code);
        $("#name").html(data.data.name);
        $("#betaka").html(data.data.BetakaDriba);
      },
      error: function () {
        console.log("error in request");
      },
    });
  });
    </script> --}}



        <script>
            $(document).ready(function() {
                $(document).on('change', '#receiverName', function() {
                    var select = $(this).val();
                    $.ajax({
                        url: `{{ URL::to('getcompany/${select}') }}`,
                        type: "GET",
                        dataType: "json",
                        // data: {
                        //   select: select,
                        // },
                        success: function(data) {
                            console.log(data);
                            $("#getReceiverName").val(data.name);
                            $("#getReceiverId").val(data.tax_id);
                            // $("#getReceiverCountry").val(data.country);
                            // $("#getReceiverGovernate").val(data.governate);
                            // $("#getReceiverRegionCity").val(data.regionCity);
                            // $("#getStreet").val(data.street);
                            // $("#getBuildingNumber").val(data.buildingNumber);
                            // $("#getPostalCode").val(data.postalCode);
                            // $("#getFloor").val(data.floor);
                            // $("#getRoom").val(data.room);
                            // $("#getLandMark").val(data.landmark);
                            // $("#getAdditional").val(data.additionalInformation);
                        },
                        error: function() {
                            console.log("error in request");
                        },
                    });
                });

            });
        </script>
