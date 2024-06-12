@extends('layouts.main')

@section('content')
    <div class="page-content">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">
                <h3> حالة الإيصال</h3>
            </div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        {{-- <li class="breadcrumb-item"><a href="javascript:;"></a></li> --}}
                        @if ($status == 'Invalid')
                            <li class="breadcrumb-item active" aria-current="page">
                                <h3 class="btn btn-danger">غير صحيح</h3>
                            </li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">
                                <h3 class="btn btn-success">صحيح</h3>
                            </li>
                        @endif
                        {{-- <li class="breadcrumb-item active" aria-current="page"><h3>{{ $status }}</h3></li> --}}
                    </ol>
                </nav>
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

                        <tr>
                            <td style="padding: 20px">الرقم الداخلى للإيصال</td>
                            <td style="padding: 20px">{{ $showData['receipts'][0]['receiptNumber'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 20px">نوع الإيصال</td>
                            <td style="padding: 20px">{{ $showData['receipts'][0]['documentTypeNameSecondaryLang'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 20px">تاريخ الإيصال</td>
                            <td style="padding: 20px">
                                {{ Carbon\Carbon::parse($showData['receipts'][0]['dateTimeIssued'])->format('d-m-Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px"> إجمالى المبلغ</td>
                            <td style="padding: 20px">{{ $showData['receipts'][0]['totalAmount'] }}
                                {{ $showData['receipts'][0]['currencyNameSecondaryLang'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 20px">حالة الإيصال</td>
                            @if ($showData['receipts'][0]['status'] == 'Valid')
                                <td style="padding: 20px">صحيح</td>
                            @else
                                <td style="padding: 20px">غير صحيح</td>
                            @endif
                        </tr>

                        @if ($showData['receipts'][0]['documentType'] == 'S')
                            <tr>
                                <td style="padding: 20px">حالة المرتجعات</td>
                                @if ($showData['receipts'][0]['hasReturnReceipts'] == 0)
                                    <td style="padding: 20px">ليس له مرتجع</td>
                                @else
                                    <td style="padding: 20px">يوجد له مرتجع</td>
                                @endif
                            </tr>
                        @endif

                        <tr>
                            @if ($showData['receipts'][0]['status'] == 'Invalid')
                                <td style="padding: 20px">الأخطاء فى الإيصال</td>
                                <td style="padding: 20px">
                                    {{ $showData['receipts'][0]['errors'][0]['error']['errorAr'] }}
                                    <hr>
                                    @foreach ($showData['receipts'][0]['errors'][0]['error']['innerError'] as $error)
                                        {{ $error['errorAr'] }}
                                        <hr>
                                    @endforeach

                                </td>
                            @else
                                <td>طباعة الإيصال</td>
                                <td><a class="btn btn-success" target="_blank"
                                        href="https://preprod.invoicing.eta.gov.eg/receipts/print/{{ $showData['receipts'][0]['uuid'] }}/share/{{ $showData['receipts'][0]['dateTimeIssued'] }}">عرض
                                        و طباعةالإيصال</a></td>
                            @endif
                        </tr>

                        </tbody>

                    </table>

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
                "paging": true,

            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
