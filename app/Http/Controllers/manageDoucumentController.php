<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class manageDoucumentController extends Controller
{

    // this is for show sent inovices
    public function sentInvoices()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        $taxId = auth()->user()->details->company_id;

        return view('invoices.sentInvoices', compact('allInvoices', 'allMeta', 'taxId'));
    }

    // this is for show recieved inovices

    public function receivedInvoices()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        $taxId = auth()->user()->details->company_id;

        return view('invoices.receivedInvoices', compact('allInvoices', 'allMeta', 'taxId'));
    }

    public function invoiceDollar(Request $request)
    {

        $validated = $request->validate([
            // 'receiverCountry' => 'required',
            // 'receiverCountry' => 'required',
            // 'receiverGovernate' => 'required',
            // 'receiverRegionCity' => 'required',
            'receiverType' => 'required',
            // 'receiverId' => 'required',
            // 'receiverName' => 'required',
            'DocumentType' => 'required',
            'date' => 'required',
            'taxpayerActivityCode' => 'required',
            'internalId' => 'required',
            'ExtraDiscount' => 'required',
            'rate' => 'required',
            'invoiceDescription' => 'required',
            'itemCode' => 'required',
            't4subtype' => 'required',
            't1subtype' => 'required',

        ]);

        $invoice =
            [
            "issuer" => array(
                "address" => array(
                    "branchID" => "0",
                    "country" => "EG",
                    "governate" => auth()->user()->details->governate,
                    "regionCity" => auth()->user()->details->regionCity,
                    "street" => auth()->user()->details->street,
                    "buildingNumber" => auth()->user()->details->buildingNumber,
                ),
                "type" => auth()->user()->details->issuerType,
                "id" => auth()->user()->details->company_id,
                "name" => auth()->user()->details->company_name,
            ),

            "receiver" => array(
                "address" => array(

                ),
                "type" => $request->receiverType,
                "id" => $request->receiverId,
                "name" => $request->receiverName,
            ),
            "documentType" => $request->DocumentType,
            "documentTypeVersion" => "1.0",
            "dateTimeIssued" => $request->date . "T" . date("h:i:s") . "Z",
            "taxpayerActivityCode" => $request->taxpayerActivityCode,
            "internalID" => $request->internalId,
            "invoiceLines" => [

            ],
            "totalDiscountAmount" => floatval($request->totalDiscountAmount),
            "totalSalesAmount" => floatval($request->TotalSalesAmount),
            "netAmount" => floatval($request->TotalNetAmount),
            "taxTotals" => array(
                array(
                    "taxType" => "T4",
                    "amount" => floatval($request->totalt4Amount),
                ),
                array(
                    "taxType" => "T1",
                    "amount" => floatval($request->totalt2Amount),
                ),
            ),
            "totalAmount" => floatval($request->totalAmount2),
            "extraDiscountAmount" => floatval($request->ExtraDiscount),
            "totalItemsDiscountAmount" => floatval($request->totalItemsDiscountAmount),
        ];

        for ($i = 0; $i < count($request->quantity); $i++) {
            $Data = [
                "description" => $request->invoiceDescription[$i],
                "itemType" => "GS1",
                "itemCode" => $request->itemCode[$i],
                // "itemCode" => "10003834",
                "unitType" => "EA",
                "quantity" => floatval($request->quantity[$i]),
                "internalCode" => "100",
                "salesTotal" => floatval($request->salesTotal[$i]),
                "total" => floatval($request->totalItemsDiscount[$i]),
                "valueDifference" => 0.00,
                "totalTaxableFees" => 0.00,
                "netTotal" => floatval($request->netTotal[$i]),
                "itemsDiscount" => floatval($request->itemsDiscount[$i]),

                "unitValue" => [
                    "currencySold" => "USD",
                    "amountSold" => floatval($request->amountSold[$i]),
                    "currencyExchangeRate" => floatval($request->currencyExchangeRate[$i]),
                    "amountEGP" => floatval($request->amountEGP[$i]),
                ],
                "discount" => [
                    "rate" => 0.00,
                    "amount" => floatval($request->discountAmount[$i]),
                ],
                "taxableItems" => [
                    [

                        "taxType" => "T4",
                        "amount" => floatval($request->t4Amount[$i]),
                        "subType" => ($request->t4subtype[$i]),
                        "rate" => floatval($request->t4rate[$i]),
                    ],
                    [
                        "taxType" => "T1",
                        "amount" => floatval($request->t2Amount[$i]),
                        "subType" => ($request->t1subtype[$i]),
                        "rate" => floatval($request->rate[$i]),
                    ],
                ],

            ];
            $invoice['invoiceLines'][$i] = $Data;
        }

// this is for receiver address

        ($request->receiverCountry ? $invoice['receiver']["address"]['country'] = $request->receiverCountry : "");
        ($request->receiverBuildingNumber ? $invoice['receiver']["address"]['buildingNumber'] = $request->receiverBuildingNumber : "");
        ($request->street ? $invoice['receiver']["address"]['street'] = $request->street : "");
        ($request->receiverRegionCity ? $invoice['receiver']["address"]['regionCity'] = $request->receiverRegionCity : "");
        ($request->receiverGovernate ? $invoice['receiver']["address"]['governate'] = $request->receiverGovernate : "");
        ($request->receiverPostalCode ? $invoice['receiver']["address"]['postalcode'] = $request->receiverPostalCode : "");
        ($request->receiverFloor ? $invoice['receiver']["address"]['floor'] = $request->receiverFloor : "");
        ($request->receiverRoom ? $invoice['receiver']["address"]['room'] = $request->receiverRoom : "");
        ($request->receiverLandmark ? $invoice['receiver']["address"]['landmark'] = $request->receiverLandmark : "");
        ($request->receiverAdditionalInformation ? $invoice['receiver']["address"]['additionalInformation'] = $request->receiverAdditionalInformation : "");
        ($request->receiverId ? $invoice['receiver']['id'] = $request->receiverId : "");

        // this is for reference debit or credit note
        ($request->referencesInvoice ? $invoice['references'] = [$request->referencesInvoice] : "");
        // End reference debit or credit note

        // this is for Bank payment

        ($request->bankName ? $invoice['payment']["bankName"] = $request->bankName : "");
        ($request->bankAddress ? $invoice['payment']["bankAddress"] = $request->bankAddress : "");
        ($request->bankAccountNo ? $invoice['payment']["bankAccountNo"] = $request->bankAccountNo : "");
        ($request->bankAccountIBAN ? $invoice['payment']["bankAccountIBAN"] = $request->bankAccountIBAN : "");
        ($request->swiftCode ? $invoice['payment']["swiftCode"] = $request->swiftCode : "");
        ($request->Bankterms ? $invoice['payment']["terms"] = $request->Bankterms : "");
        // End Bank payment

        $trnsformed = json_encode($invoice, JSON_UNESCAPED_UNICODE);
        $myFileToJson = fopen("D:\laragon\www\cwatro\EInvoicing\SourceDocumentJson.json", "w") or die("unable to open file");
        fwrite($myFileToJson, $trnsformed);
        return redirect()->route('cer');

    }
    public function invoice(Request $request)
    {

        $validated = $request->validate([
            // 'receiverCountry' => 'required',
            // 'receiverCountry' => 'required',
            // 'receiverGovernate' => 'required',
            // 'receiverRegionCity' => 'required',
            'receiverType' => 'required',
            // 'receiverId' => 'required',
            // 'receiverName' => 'required',
            'DocumentType' => 'required',
            'date' => 'required',
            'taxpayerActivityCode' => 'required',
            'internalId' => 'required',
            'ExtraDiscount' => 'required',
            'rate' => 'required',
            'invoiceDescription' => 'required',
            'itemCode' => 'required',
            't4subtype' => 'required',
            't1subtype' => 'required',

        ]);

        $invoice =
            [
            "issuer" => array(
                "address" => array(
                    "branchID" => "0",
                    "country" => "EG",
                    "governate" => auth()->user()->details->governate,
                    "regionCity" => auth()->user()->details->regionCity,
                    "street" => auth()->user()->details->street,
                    "buildingNumber" => auth()->user()->details->buildingNumber,
                ),
                "type" => auth()->user()->details->issuerType,
                "id" => auth()->user()->details->company_id,
                "name" => auth()->user()->details->company_name,
            ),

            "receiver" => array(
                "address" => array(

                ),
                "type" => $request->receiverType,
                "name" => $request->receiverName,
            ),
            "documentType" => $request->DocumentType,
            "documentTypeVersion" => "1.0",
            "dateTimeIssued" => $request->date . "T" . date("h:i:s") . "Z",
            "taxpayerActivityCode" => $request->taxpayerActivityCode,
            "internalID" => $request->internalId,
            "invoiceLines" => [

            ],
            "totalDiscountAmount" => floatval($request->totalDiscountAmount),
            "totalSalesAmount" => floatval($request->TotalSalesAmount),
            "netAmount" => floatval($request->TotalNetAmount),
            "taxTotals" => array(
                array(
                    "taxType" => "T4",
                    "amount" => floatval($request->totalt4Amount),
                ),
                array(
                    "taxType" => "T1",
                    "amount" => floatval($request->totalt2Amount),
                ),
            ),
            "totalAmount" => floatval($request->totalAmount2),
            "extraDiscountAmount" => floatval($request->ExtraDiscount),
            "totalItemsDiscountAmount" => floatval($request->totalItemsDiscountAmount),
        ];

        for ($i = 0; $i < count($request->quantity); $i++) {
            $Data = [
                "description" => $request->invoiceDescription[$i],
                "itemType" => "GS1",
                "itemCode" => $request->itemCode[$i],
                // "itemCode" => "10003834",
                "unitType" => "EA",
                "quantity" => floatval($request->quantity[$i]),
                "internalCode" => "100",
                "salesTotal" => floatval($request->salesTotal[$i]),
                "total" => floatval($request->totalItemsDiscount[$i]),
                "valueDifference" => 0.00,
                "totalTaxableFees" => 0.00,
                "netTotal" => floatval($request->netTotal[$i]),
                "itemsDiscount" => floatval($request->itemsDiscount[$i]),

                "unitValue" => [
                    "currencySold" => "EGP",
                    "amountSold" => 0.00,
                    "currencyExchangeRate" => 0.00,
                    "amountEGP" => floatval($request->amountEGP[$i]),
                ],
                "discount" => [
                    "rate" => 0.00,
                    "amount" => floatval($request->discountAmount[$i]),
                ],
                "taxableItems" => [
                    [

                        "taxType" => "T4",
                        "amount" => floatval($request->t4Amount[$i]),
                        "subType" => ($request->t4subtype[$i]),
                        "rate" => floatval($request->t4rate[$i]),
                    ],
                    [
                        "taxType" => "T1",
                        "amount" => floatval($request->t2Amount[$i]),
                        "subType" => ($request->t1subtype[$i]),
                        "rate" => floatval($request->rate[$i]),
                    ],
                ],

            ];
            $invoice['invoiceLines'][$i] = $Data;
        }

        // this is for receiver address

        ($request->receiverCountry ? $invoice['receiver']["address"]['country'] = $request->receiverCountry : "");
        ($request->receiverBuildingNumber ? $invoice['receiver']["address"]['buildingNumber'] = $request->receiverBuildingNumber : "");
        ($request->street ? $invoice['receiver']["address"]['street'] = $request->street : "");
        ($request->receiverRegionCity ? $invoice['receiver']["address"]['regionCity'] = $request->receiverRegionCity : "");
        ($request->receiverGovernate ? $invoice['receiver']["address"]['governate'] = $request->receiverGovernate : "");
        ($request->receiverPostalCode ? $invoice['receiver']["address"]['postalcode'] = $request->receiverPostalCode : "");
        ($request->receiverFloor ? $invoice['receiver']["address"]['floor'] = $request->receiverFloor : "");
        ($request->receiverRoom ? $invoice['receiver']["address"]['room'] = $request->receiverRoom : "");
        ($request->receiverLandmark ? $invoice['receiver']["address"]['landmark'] = $request->receiverLandmark : "");
        ($request->receiverAdditionalInformation ? $invoice['receiver']["address"]['additionalInformation'] = $request->receiverAdditionalInformation : "");
        ($request->receiverId ? $invoice['receiver']['id'] = $request->receiverId : "");

        // this is for reference debit or credit note
        ($request->referencesInvoice ? $invoice['references'] = [$request->referencesInvoice] : "");
        // End reference debit or credit note

        // this is for Bank payment

        ($request->bankName ? $invoice['payment']["bankName"] = $request->bankName : "");
        ($request->bankAddress ? $invoice['payment']["bankAddress"] = $request->bankAddress : "");
        ($request->bankAccountNo ? $invoice['payment']["bankAccountNo"] = $request->bankAccountNo : "");
        ($request->bankAccountIBAN ? $invoice['payment']["bankAccountIBAN"] = $request->bankAccountIBAN : "");
        ($request->swiftCode ? $invoice['payment']["swiftCode"] = $request->swiftCode : "");
        ($request->Bankterms ? $invoice['payment']["terms"] = $request->Bankterms : "");
        // End Bank payment

        $trnsformed = json_encode($invoice, JSON_UNESCAPED_UNICODE);
        $myFileToJson = fopen("D:\laragon\www\cwatro\EInvoicing\SourceDocumentJson.json", "w") or die("unable to open file");
        fwrite($myFileToJson, $trnsformed);
        return redirect()->route('cer');

    }

// this function for signature

    public function openBat()
    {

        shell_exec('D:\laragon\www\cwatro\EInvoicing/SubmitInvoices2.bat');
        $path = "D:\laragon\www\cwatro\EInvoicing/FullSignedDocument.json";
        $path2 = "D:\laragon\www\cwatro\EInvoicing/Cades.txt";
        $path3 = "D:\laragon\www\cwatro\EInvoicing/CanonicalString.txt";
        $path4 = "D:\laragon\www\cwatro\EInvoicing/SourceDocumentJson.json";

        $fullSignedFile = file_get_contents($path);

        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $invoice = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->withBody($fullSignedFile, "application/json")->post('https://api.preprod.invoicing.eta.gov.eg/api/v1/documentsubmissions');

        if ($invoice['submissionId'] == !null) {
            unlink($path);
            unlink($path2);
            unlink($path3);
            unlink($path4);
            return redirect()->route('sentInvoices')->with('success', 'تم تسجيل الفاتورة بنجاح ');
            // return $invoice->body();

        } else {
            unlink($path);
            unlink($path2);
            unlink($path3);
            unlink($path4);
            // return $invoice->body();
            return redirect()->route('sentInvoices')->with('error', "يوجد خطأ فى الفاتورة من فضلك اعد تسجيلها");
        }
    }

// this is for create page of invoice
    public function createInvoice()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $product = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();
        return view('invoices.createInvoice2', compact('allCompanies', 'codes', 'ActivityCodes', 'taxTypes', 'products'));
    }

    // this function for Fill  the customer information

    public function createInvoice2(Request $request)
    {

        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $product = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();
        $companiess = DB::table('companies2')->where('id', $request->receiverName)->get();
        return view('invoices.createInvoice2', compact('companiess', 'allCompanies', "codes", 'ActivityCodes', 'taxTypes', "products"));
    }

    public function createInvoiceDollar()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $product = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();
        return view('invoices.createInvoice3', compact('allCompanies', 'codes', 'ActivityCodes', 'taxTypes', 'products'));
    }

    // this function for Fill  the customer information

    public function createInvoiceDollar2(Request $request)
    {

        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $product = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();
        $companiess = DB::table('companies2')->where('id', $request->receiverName)->get();
        return view('invoices.createInvoice3', compact('companiess', 'allCompanies', "codes", 'ActivityCodes', 'taxTypes', "products"));
    }

    public function createInvoice3()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $product = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();
        return view('invoices.createInvoice3', compact('allCompanies', 'codes', 'ActivityCodes', 'taxTypes', 'products'));
    }

    // this function for Fill  the customer information

    public function createInvoice4(Request $request)
    {

        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $product = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Content-Type" => "application/json",
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/codetypes/requests/my?Active=true&Status=Approved&PS=1000');

        $products = $product['result'];
        $codes = DB::table('products')->where('status', 'Approved')->get();
        $ActivityCodes = DB::table('activity_code')->get();
        $allCompanies = DB::table('companies2')->get();
        $taxTypes = DB::table('taxtypes')->get();
        $companiess = DB::table('companies2')->where('id', $request->receiverName)->get();
        return view('invoices.createInvoice3', compact('companiess', 'allCompanies', "codes", 'ActivityCodes', 'taxTypes', "products"));
    }

// show pdf printout
    public function showPdfInvoice($uuid)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showPdf = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
            "Accept-Language" => 'ar',
        ])->get("https://api.preprod.invoicing.eta.gov.eg/api/v1/documents/" . $uuid . "/pdf");

        return response($showPdf)->header('Content-Type', 'application/pdf');
    }

    public function cancelDocument($uuid)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $cancel = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->put(
            'https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/state/' . $uuid . '/state',
            array(
                "status" => "cancelled",
                "reason" => "يوجد خطأ بالفاتورة",
            )
        );
        // return ($cancel);
        if ($cancel->ok()) {
            return redirect()->route('sentInvoices')->with('success', 'تم تقديم طلب الغاء الفاتورة بنجاح سيتم الموافقة او الرفض فى خلال 3 ايام');
        } else {
            return redirect()->route('sentInvoices')->with('error', $cancel['error']['details'][0]['message']);
        }
    }

    public function RejectDocument($uuid)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $cancel = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->put(
            'https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/state/' . $uuid . '/state',
            array(
                "status" => "rejected",
                "reason" => "يوجد خطأ بالفاتورة",
            )
        );
        // return ($cancel);
        if ($cancel->ok()) {
            return redirect()->route('receivedInvoices')->with('success', 'تم تقديم طلب رفض الفاتورة بنجاح سيتم الموافقة او الرفض فى خلال 3 ايام');
        } else {
            return redirect()->route('receivedInvoices')->with('error', $cancel['error']['details'][0]['message']);
        }


    }




    public function DeclineRejectDocument($uuid)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $cancel = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->put(
            'https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/state/' . $uuid . '/decline/rejection');
        // return ($cancel);
        if ($cancel->ok()) {
            return redirect()->back()->with('success', 'تم الغاء الرفض بنجاح');
        } else {
            return redirect()->back()->with('error', $cancel['error']['details'][0]['message']);
        }
    }

    public function DeclineCancelDocument($uuid)
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $cancel = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->put(
            'https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/state/' . $uuid . '/decline/cancelation');
        // return ($cancel);
        if ($cancel->ok()) {
            return redirect()->back()->with('success', 'تم الغاء الإلغاء بنجاح');
        } else {
            return redirect()->back()->with('error', $cancel['error']['details'][0]['message']);
        }
    }

    public function RequestcancelledDoc()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.requestCancelled', compact('allInvoices', 'allMeta'));
    }

    public function companiesRequestcancelledDoc()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.companiesRequestCancelled', compact('allInvoices', 'allMeta'));
    }

    public function cancelledDoc()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.showCancelled', compact('allInvoices', 'allMeta'));
    }

    public function companyCancelledDoc()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.showCompanyCancelled', compact('allInvoices', 'allMeta'));
    }

    public function rejected()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.showRejected', compact('allInvoices', 'allMeta'));
    }

    public function companyRejected()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.companyRejected', compact('allInvoices', 'allMeta'));
    }

    public function requestCompanyRejected()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.RequestCompanyRejected', compact('allInvoices', 'allMeta'));
    }

    public function requestRejected()
    {
        $response = Http::asForm()->post('https://id.preprod.eta.gov.eg/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => auth()->user()->details->client_id,
            'client_secret' => auth()->user()->details->client_secret,
            'scope' => "InvoicingAPI",
        ]);

        $showInvoices = Http::withHeaders([
            "Authorization" => 'Bearer ' . $response['access_token'],
        ])->get('https://api.preprod.invoicing.eta.gov.eg/api/v1.0/documents/recent?pageSize=2000000000');

        $allInvoices = $showInvoices['result'];

        $allMeta = $showInvoices['metadata'];
        return view('invoices.RequestRejected', compact('allInvoices', 'allMeta'));
    }

}
