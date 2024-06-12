@extends('layouts.main')

@section('content')
    <div class="page-content">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">الإرسالات</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">الإرسالات</li>
                    </ol>
                </nav>
            </div>
            {{-- <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('createreceipt') }}" class="btn btn-outline-success px-5 radius-30">
                        <i class="bx bx-message-square-edit mr-1"></i>جميع الإرسالات</a>

                </div>
            </div> --}}
        </div>

        {{-- <div>
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
        </div> --}}

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
                                <th>رقم الإرسال</th>
                                <th>تاريخ الإيصال</th>
                                <th>إجمالى الإيصال</th>
                                <th> عرض البيانات و حالة الإيصال</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($allsubmisstion as  $receipt)
                                <tr>
                                    @foreach ($receipt['jsondata'] as $data)
                                    <td>{{ $data[0]['header']['receiptNumber'] }}</td>
                                    @endforeach
                                    <td>{{ $receipt->submissionUuid }}</td>
                                   @foreach ($receipt['jsondata'] as $data)
                                   <td>{{ Carbon\Carbon::parse($data[0]['header']['dateTimeIssued'])->format('d-m-Y') }} </td>
                                   @endforeach
                                   @foreach ($receipt['jsondata'] as $data)
                                   <td>{{ $data[0]['totalAmount'] }}</td>
                                   @endforeach
                                   <td><a class="btn btn-success" href="{{ route('returnSubmission',$receipt->submissionUuid) }}">عرض</a></td>
                                </tr>
                            @endforeach


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
