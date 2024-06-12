<?php

namespace App\Http\Controllers;

use App\Models\Submissionuuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ReceiptController extends Controller
{
    public $baseUrl = 'https://id.preprod.eta.gov.eg';
    public $baseUrl1 = 'https://api.preprod.invoicing.eta.gov.eg';

    public function showRecentReceipt($id)
    {
        $response = Http::asForm()->post("$this->baseUrl/connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => 'InvoicingAPI',
        ]);

        // return $response;

        $showReceipts = Http::withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token'],
        ])->get("$this->baseUrl1/api/v1/receipts/recent?PageNo=$id&PageSize=10");

        // return $showReceipts;

        $allReceipts = $showReceipts['receipts'];

        $allMeta = $showReceipts['metadata'];
        // $taxId = auth()->user()->details->company_id;
        // return $allReceipts;
        return view('receipt.showRecent', compact('allReceipts', 'id', 'allMeta'));
    }

    public function searchReceipts(Request $request)
    {
        // Get parameters directly from the request
        $id = $request->id;
        $freetext = $request->freetext;
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $receiverName = $request->receiverName;
        $direction = $request->Direction;
        $documentTypeCode = $request->DocumentTypeCode;
        $url = explode('?', $request->fullUrl());

        // Get the access token
        $response = Http::asForm()->post("$this->baseUrl/connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => 'InvoicingAPI',
        ]);

        // Check if authentication was successful before proceeding
        if (!$response->successful()) {
            return back()->with('error', 'Failed to authenticate API request.');
        }

        // Make the API call
        $showReceipts = Http::withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token'],
        ])->get("$this->baseUrl1/api/v1/receipts/search?PageNo=$id&PageSize=100&FreeText=$freetext&DateTimeIssuedFrom=$datefrom&DateTimeIssuedTo=$dateto&ReceiverName=$receiverName&Direction=$direction&DocumentTypeCode=$documentTypeCode");

        // Process the response
        if ($showReceipts->ok() && isset($showReceipts['receipts'])) {
            $allReceipts = $showReceipts['receipts'];
            $allMeta = $showReceipts['metadata'];
            return view('receipt.showRecent', compact('allReceipts', 'id', 'allMeta', 'url'));
        } else {
            return redirect()->route('showRecentReceipt', '1')->with('error', 'No search results found.');
        }
    }

    // create new receipt
    public function createReceipt()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => 'InvoicingAPI',
        ]);

        $product = Http::withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token'],
            'Content-Type' => 'application/json',
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();

        return view('receipt.create', compact('allCompanies', 'codes', 'ActivityCodes', 'taxTypes', 'products'));
    }

    public function sendReceipt(Request $request)
    {
        [
            $receipt =
                [
                    'header' => array(
                        'dateTimeIssued' => $request->date . 'T' . date('h:i:s') . 'Z',  // mandatory
                        'receiptNumber' => $request->receiptNumber,  // mandatory
                        'uuid' => '',  // mandatory
                        'previousUUID' => '',  // mandatory if not first receipt
                        'referenceOldUUID' => '',  // optional
                        'currency' => 'EGP',  // mandatory
                        'exchangeRate' => 0,  // mandatory
                        'sOrderNameCode' => '',  // optional
                        'orderdeliveryMode' => 'FC',  // mandatory
                        //  "grossWeight" => 6.58, // optional
                        // "netWeight" => 6.89, // optional
                    ),
                    'documentType' => array(
                        'receiptType' => 'S',  // mandatory
                        'typeVersion' => '1.2',  // mandatory
                    ),
                    'seller' => array(
                        'rin' => auth()->user()->details->company_id,  // mandatory
                        'companyTradeName' => auth()->user()->details->company_name,  // mandatory
                        'branchCode' => '0',  // mandatory
                        'branchAddress' => array(
                            'country' => 'EG',  // mandatory
                            'governate' => auth()->user()->details->governate,  // mandatory
                            'regionCity' => auth()->user()->details->regionCity,  // mandatory
                            'street' => auth()->user()->details->street,  // mandatory
                            'buildingNumber' => auth()->user()->details->buildingNumber,  // mandatory
                            // "postalCode" => "74235", // optional
                            // "floor" => "1F", // optional
                            // "room" => "3R", // optional
                            // "landmark" => "tahrir square", // optional
                            // "additionalInformation" => "talaat harb street", // optional
                        ),
                        'deviceSerialNumber' => auth()->user()->details->posserialnumber,  // mandatory
                        // "syndicateLicenseNumber" => "1000056", // optional
                        'activityCode' => $request->taxpayerActivityCode,  // mandatory
                    ),
                    'buyer' => array(
                        'type' => $request->receiverType,  // mandatory
                        // "id" => $request->receiverId, // Optional in all cases except when.type is B.type is P and totalAmount equals to or greater than a configured value (ex. 50000 EGP)
                        // "name" =>  $request->receiverName, // Optional in all cases except when.type is B.type is P and totalAmount equals to or greater than a configured value (ex. 50000 EGP)
                        // "mobileNumber" => $request->mobileNumber, // optional
                        // "paymentNumber" => $request->paymentNumber, // optional
                    ),
                    'itemData' => [
                        [
                            // "internalCode" => "100", // mandatory
                            // "description" => $request->invoiceDescription, // mandatory
                            // "itemType" => "GS1", // mandatory
                            // "itemCode" => $request->itemCode, // mandatory
                            // "unitType" => "EA", // mandatory
                            // "quantity" => floatval($request->quantity), // mandatory
                            // "unitPrice" => floatval($request->amountEGP), // mandatory
                            // "netSale" => floatval($request->netTotal), // mandatory
                            // "totalSale" => floatval($request->salesTotal), // mandatory
                            // "total" => floatval($request->totalItemsDiscount), // mandatory
                            // "commercialDiscountData" => [ //optional
                            //     [
                            //         "amount" => floatval($request->discountAmount),
                            //         "description" => "خصم تجارى",
                            //     ],
                            // ],
                            // "itemDiscountData" => [
                            //     [
                            //         "amount" => floatval($request->itemsDiscount),
                            //         "description" => "خصم اصناف",
                            //     ],
                            //     // [
                            //     //     "amount" => 10,
                            //     //     "description" => "XYZ",
                            //     // ],
                            // ],
                            // "valueDifference" => 0,
                            // "taxableItems" => [
                            //     [
                            //         "taxType" => "T1",
                            //         "amount" => floatval($request->t2Amount),
                            //         "subType" => $request->t1subtype,
                            //         "rate" => floatval($request->rate),
                            //     ],
                            // ],
                        ],
                    ],
                    'totalSales' => floatval($request->TotalSalesAmount),
                    'totalItemsDiscount' => floatval($request->totalItemsDiscountAmount),
                    'extraReceiptDiscountData' => [
                        [
                            'amount' => floatval($request->ExtraDiscount),
                            'description' => 'ABC',
                        ],
                    ],
                    'netAmount' => floatval($request->TotalNetAmount),
                    'feesAmount' => 0,
                    'totalAmount' => floatval($request->totalAmount2),
                    'taxTotals' => [
                        [
                            'taxType' => 'T1',
                            'amount' => floatval($request->totalt2Amount),
                        ],
                    ],
                    'paymentMethod' => 'C',
                ]
        ];

        for ($i = 0; $i < count($request->quantity); $i++) {
            $Data = [
                // "description" => $request->invoiceDescription[$i],
                // "itemType" => "GS1",
                // "itemCode" => $request->itemCode[$i],
                // "itemCode" => "10003834",
                // "unitType" => "EA",
                // "quantity" => floatval($request->quantity[$i]),
                // "internalCode" => "100",
                // "salesTotal" => floatval($request->salesTotal[$i]),
                // "total" => floatval($request->totalItemsDiscount[$i]),
                // "valueDifference" => 0.00,
                // "totalTaxableFees" => 0.00,
                // "netTotal" => floatval($request->netTotal[$i]),
                // "itemsDiscount" => floatval($request->itemsDiscount[$i]),
                'internalCode' => '100',  // mandatory
                'description' => $request->invoiceDescription[$i],  // mandatory
                'itemType' => 'GS1',  // mandatory
                'itemCode' => $request->itemCode[$i],  // mandatory
                'unitType' => 'EA',  // mandatory
                'quantity' => floatval($request->quantity[$i]),  // mandatory
                'unitPrice' => floatval($request->amountEGP[$i]),  // mandatory
                'netSale' => floatval($request->netTotal[$i]),  // mandatory
                'totalSale' => floatval($request->salesTotal[$i]),  // mandatory
                'total' => floatval($request->totalItemsDiscount[$i]),  // mandatory
                // "commercialDiscountData" => [ //optional
                //     [
                //         "amount" => floatval($request->discountAmount[$i]),
                //         "description" => "خصم تجارى",
                //     ],
                // ],
                'itemDiscountData' => [
                    [
                        'amount' => floatval($request->itemsDiscount[$i]),
                        'description' => 'خصم اصناف',
                    ],
                ],
                'valueDifference' => 0,
                'taxableItems' => [
                    [
                        'taxType' => 'T1',
                        'amount' => floatval($request->t2Amount[$i]),
                        'subType' => $request->t1subtype[$i],
                        'rate' => floatval($request->rate[$i]),
                    ],
                ],
            ];
            ($request->discountAmount[$i] > 0 ? $Data['commercialDiscountData'][0]['amount'] = floatval($request->discountAmount[$i]) : '');
            ($request->discountAmount[$i] > 0 ? $Data['commercialDiscountData'][0]['description'] = 'خصم تجارى' : '');
            $receipt['itemData'][$i] = $Data;
        }

        ($request->totalDiscountAmount > 0 ? $receipt['totalCommercialDiscount'] = floatval($request->totalDiscountAmount) : '');
        ($request->receiverName ? $receipt['buyer']['name'] = $request->receiverName : '');
        ($request->receiverId ? $receipt['buyer']['id'] = $request->receiverId : '');
        ($request->mobileNumber ? $receipt['buyer']['mobileNumber'] = $request->mobileNumber : '');
        ($request->paymentNumber ? $receipt['buyer']['paymentNumber'] = $request->paymentNumber : '');

        $trnsformed = json_encode($receipt, JSON_UNESCAPED_UNICODE);
        $myFileToJson = fopen('D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json', 'w') or die('unable to open file');
        fwrite($myFileToJson, $trnsformed);
        // shell_exec('D:\laragon\www\ereceipt\EInvoicing/SubmitInvoices2.bat');
        return redirect()->route('hashCanonical');
        // return $receipt;
    }

    public function sha256()
    {
        shell_exec('D:\laragon\www\ereceipt\EInvoicing/SubmitInvoices2.bat');
        $content = file_get_contents('D:\laragon\www\ereceipt\EInvoicing\CanonicalString.txt');
        $uuid = hash('sha256', $content);
        return redirect()->route('canonical')->with(['uuid' => $uuid]);
    }

    public function canonical()
    {
        // shell_exec('D:\laragon\www\ereceipt\EInvoicing/SubmitInvoices2.bat');
        $uuid = Session::get('uuid');
        $data = file_get_contents('D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json');
        $obj = json_decode($data, true);
        $uuid = $obj['header']['uuid'] = $uuid;
        $trnsformed = json_encode(['receipts' => [$obj]], JSON_UNESCAPED_UNICODE);
        $myFileToJson = fopen('D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json', 'w') or die('unable to open file');
        $file = fwrite($myFileToJson, $trnsformed);

        return redirect()->route('finalStep');
    }

    public function finalStep()
    {
        // $finalJson = Session::get('jsondata');
        $path = 'D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json';
        $fullSignedFile = file_get_contents($path);

        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => 'InvoicingAPI',
        ]);

        $receipt = Http::withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token'],
            'Content-Type' => 'application/json',
        ])->withBody($fullSignedFile, 'application/json')->post('https://api.preprod.invoicing.eta.gov.eg/api/v1/receiptsubmissions');

        // return $receipt;
        if (isset($receipt['submissionId'])) {
            $submisstion = new Submissionuuid();
            $submisstion->submissionUuid = $receipt['submissionId'];
            $submisstion->jsondata = json_decode($fullSignedFile);
            $submisstion->save();

            return redirect()->route('showRecentReceipt', '1')->with('success', ' تم تسجيل الإيصال بنجاح ' . ' برجاء التأكد من منه اذا كان صحيحاً ' . ' برقم ' . $receipt['acceptedDocuments'][0]['receiptNumber']);
        } else {
            foreach ($receipt['rejectedDocuments'][0]['error']['details'] as $rec) {
                return redirect()->route('showRecentReceipt', '1')->with('error', $rec['message'] . $rec['propertyPath'] . '<br>' . 'يوجد خطأ بالإيصال فى ' . '<br><br>');
            }
            // return $receipt["rejectedDocuments"][0]['receiptNumber'];
        }
    }

    public function returnReceipt($uuid)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => 'InvoicingAPI',
        ]);

        $receipt = Http::withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token'],
            'Content-Type' => 'application/json',
        ])->get("https://api.preprod.invoicing.eta.gov.eg/api/v1/receipts/$uuid/raw");
        // $new = json_encode($receipt['rawDocument']);
        $trnsformed = $receipt['rawDocument'];
        $myFileToJson = fopen('D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json', 'w') or die('unable to open file');
        fwrite($myFileToJson, $trnsformed);

        $data = file_get_contents('D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json');
        $obj = json_decode($data, true);
        $newUUid = $obj['header']['uuid'] = '';
        $referenceUUID = $obj['header']['referenceUUID'] = $uuid;
        $datetime = $obj['header']['dateTimeIssued'] = date('Y-m-d') . 'T' . date('H:i:s') . 'Z';
        $receiptType = $obj['documentType']['receiptType'] = 'R';
        $trnsformed = json_encode($obj, JSON_UNESCAPED_UNICODE);
        $myFileToJson = fopen('D:\laragon\www\ereceipt\EInvoicing\SourceDocumentJson.json', 'w') or die('unable to open file');
        $file = fwrite($myFileToJson, $trnsformed);

        return redirect()->route('hashCanonical');

        // return $receipt['rawDocument'];
    }

    public function showSubmission()
    {
        $allsubmisstion = Submissionuuid::orderBy('id', 'desc')->get();
        return view('submissions.showsubmisstion', compact('allsubmisstion'));
        // return $allsubmisstion;
        // foreach($allsubmisstion as $data){
        //     foreach($data['jsondata'] as $data2){
        //         echo $data2[0]['totalAmount'] . ' <br>'.$data2[0]['header']['receiptNumber']. ' <br>'.;
        //         return $data2;
        //     }
        // }
    }

    public function returnSubmission($submisstionId)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => 'InvoicingAPI',
        ]);

        $showData = Http::withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token'],
            'Accept-Language' => 'ar',
        ])->get("$this->baseUrl1/api/v1/receiptsubmissions/$submisstionId/details?PageNo=1&PageSize=10");

        $status = $showData['status'];

        return view('submissions.submissindetails', compact('status', 'showData'));

        // return $showData;
    }
};
