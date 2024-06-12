@extends('layouts.main')

@section('content')
    <div class="page-content">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">الإيصالات</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">الإيصالات المرسلة</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('createreceipt') }}" class="btn btn-outline-success px-5 radius-30">
                        <i class="bx bx-message-square-edit mr-1"></i>إضافة إيصال</a>

                </div>
            </div>
        </div>

        <div>
            <p>

                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                    aria-expanded="false" aria-controls="collapseExample">
                    بحث متقدم
                </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <form action="{{ route('searchReceipts', '1') }}">
                        <div class="row">

                            <div class="mb-3 col-3">
                                <label for="exampleInputEmail1" class="form-label">بحث حر (يجب ان يكون 5 حروف او
                                    اكثر)</label>
                                <input type="text" minlength="5" name="freetext" class="form-control">

                            </div>
                            <div class="mb-3 col-3">
                                <label for="" class="form-label">إتجاه الإيصال</label>
                                <select name="Direction" id="" class="form-control">
                                    <option value="">اختر اتجاه الإيصال...</option>
                                    <option value="Submitted">ايصالات تم ارسالها</option>
                                    <option value="Received">ايصالات تم إستقبالها</option>
                                </select>

                            </div>
                            <div class="mb-3 col-3">
                                <label for="" class="form-label">إتجاه الإيصال</label>
                                <select name="DocumentTypeCode" id="" class="form-control">
                                    <option value="">اختر نوع الإيصال...</option>
                                    <option value="S">إيصال بيع</option>
                                    <option value="R">إيصال مرتجع</option>
                                </select>

                            </div>
                            <div class="mb-3 col-3">
                                <label for="exampleInputEmail1" class="form-label">إسم المُتلقى</label>
                                <input type="text" name="receiverName" class="form-control">

                            </div>
                            <div class="mb-3 col-3">
                                <label for="exampleInputPassword1" class="form-label">التاريخ من</label>
                                <input type="date" name="datefrom" class="form-control">
                            </div>
                            <div class="mb-3 col-3">
                                <label for="exampleInputPassword1" class="form-label">التاريخ الى</label>
                                <input type="date" name="dateto" class="form-control">
                            </div>

                            <div class="col-12" style="text-align: center;margin:20px">

                                <button type="submit" class="btn btn-primary" style="width: 200px">بحـــث</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin: 20px">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li style="list-style: none;font-size:25px">{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif


            @if (\Session::has('error'))
                <div class="alert alert-danger">
                    <ul>
                        <li style="list-style: none;font-size:25px">{!! \Session::get('error') !!}</li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered text-center">
                        <thead>
                            <tr>
                                <th>الرقم الداخلى للإيصال</th>
                                <th>الرقم الإلكترونى للإيصال</th>
                                <th>الإسم</th>
                                <th>نوع الإيصال</th>
                                <th>حالة الإيصال</th>
                                <th>إتجاه الإيصال</th>
                                {{-- <th>موقف المرتجع للإيصال</th> --}}
                                <th>التاريخ</th>
                                <th>إجمالى المبلغ</th>
                                <th>طباعة Qr-Code</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($allReceipts as $index => $receipt)
                                <tr>
                                    <td>{{ $receipt['receiptNumber'] }}</td>
                                    <td>{{ $receipt['uuid'] }}</td>
                                    @if (isset($receipt['receiverName']))
                                        <td>{{ $receipt['receiverName'] }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ $receipt['documentTypeNameSecondaryLang'] }}</td>
                                    @if ($receipt['status'] === 'Valid')
                                        <td>صحيح</td>
                                    @else
                                        <td>{{ $receipt['status'] }}</td>
                                    @endif
                                    @if ($receipt['direction'] == 'Submitted')
                                        <td>إيصال تم ارساله</td>
                                    @elseif($receipt['direction'] == 'Received')
                                        <td style="background-color: red;opacity:0.6;color:white">إيصال تم استقباله</td>
                                    @else
                                        <td>{{ $receipt['direction'] }}</td>
                                    @endif

                                    {{-- @if ($receipt['hasReturnReceipts'] == 1)
                                        <td>نعم</td>
                                    @elseif($receipt['hasReturnReceipts'] == 0)
                                        <td>لا</td>
                                    @else
                                    <td>{{ $receipt['hasReturnReceipts'] }}</td>
                                    @endif --}}
                                    <td>{{ Carbon\Carbon::parse($receipt['dateTimeIssued'])->format('d-m-Y') }}</td>
                                    <td>{{ $receipt['totalAmount'] }} {{ $receipt['currencyNameSecondaryLang'] }}</td>
                                    <td>
                                        @if ($receipt['typeName'] == 'S' && $receipt['direction'] == 'Submitted' && $receipt['hasReturnReceipts'] == 0)
                                            <a class="btn btn-success" target="_blank"
                                                href="https://preprod.invoicing.eta.gov.eg/receipts/print/{{ $receipt['uuid'] }}/share/{{ $receipt['dateTimeIssued'] }}">عرض
                                                و طباعة الإيصال</a>
                                            <a class="btn btn-danger" href="{{ route('returnReceipt', $receipt['uuid']) }}"
                                                onclick="return confirm('هل أنت متأكد من إنشاء مرتجع لهذا الإيصال؟');">إنشاء
                                                مرتجع</a>
                                        @else
                                            <a class="btn btn-success" target="_blank"
                                                href="https://preprod.invoicing.eta.gov.eg/receipts/print/{{ $receipt['uuid'] }}/share/{{ $receipt['dateTimeIssued'] }}">عرض
                                                و طباعةالإيصال</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>

                    </table>
                    @if (request()->routeIs('showRecentReceipt'))
                        <nav aria-label="Page navigation example">

                            {{-- {{ $allMeta['totalPages'] }} --}}

                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" {{ $id == 1 ? 'style=display:none' : '' }}
                                        href="{{ route('showRecentReceipt', $id - 1) }}">السابق</a></li>
                                @for ($i = 1; $i <= $allMeta['totalPages']; $i++)
                                    <li class="page-item"><a class="page-link"
                                            {{ $i == $id ? 'style=background-color:#CCC' : '' }}
                                            href="{{ route('showRecentReceipt', $i) }}">{{ $i }}</a></li>
                                @endfor
                                <li class="page-item"><a class="page-link"
                                        {{ $id == $allMeta['totalPages'] ? 'style=display:none' : '' }}
                                        href="{{ route('showRecentReceipt', $id + 1) }}">التالى</a></li>


                            </ul>
                        </nav>
                    @else
                        <nav aria-label="Page navigation example">
                            {{-- {{$url[1]}} --}}
                            {{-- {{ $allMeta['totalPages'] }} --}}

                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" {{ $id == 1 ? 'style=display:none' : '' }}
                                        href="{{ route('searchReceipts', $id - 1) }}?{{ $url[1] }}">السابق</a></li>
                                @for ($i = 1; $i <= $allMeta['totalPages']; $i++)
                                    <li class="page-item"><a class="page-link"
                                            {{ $i == $id ? 'style=background-color:#CCC' : '' }}
                                            href="{{ route('searchReceipts', $i) }}?{{ $url[1] }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                <li class="page-item"><a class="page-link"
                                        {{ $id == $allMeta['totalPages'] ? 'style=display:none' : '' }}
                                        href="{{ route('searchReceipts', $id + 1) }}?{{ $url[1] }}">التالى</a></li>


                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script src="{{ asset('main/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('main/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'print'],
                sort: false,
                "paging": false,
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
