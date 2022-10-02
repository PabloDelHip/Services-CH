<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Openpay;
use Exception;
use OpenpayApiError;
use OpenpayApiAuthError;
use OpenpayApiRequestError;
use OpenpayApiConnectionError;
use OpenpayApiTransactionError;
use Illuminate\Http\JsonResponse;

require_once '../vendor/autoload.php';

class OpenPayController extends Controller
{
    /**
     * Create charge in OpenPay
     * https://www.openpay.mx/docs/api/?php#con-id-de-tarjeta-o-token
     * 
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            // create instance OpenPay
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_SK'));
            
            Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));
            
            // create object customer
            $customer = array(
                'name' => $data["customer"]["name"],
                'last_name' => $data["customer"]["last_name"],
                'email' => $data["customer"]["email"]
            );

            // create object charge
            $chargeRequest =  array(
                'method' => 'card',
                'source_id' => $data["tokens"]["token_id"],
                'amount' => $data["total_sale"],
                'currency' => 'MXN',
                'description' => 'Pago desde sitio web caribbeanhollidays.com',
                'device_session_id' => $data["tokens"]["deviceIdHiddenFieldName"],
                'customer' => $data["customer"],
            );

            $charge = $openpay->charges->create($chargeRequest);

            return response()->json([
                'data' => $charge->id,
                "completed" => true,
            ]);

        } catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e,
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }
    }
}
