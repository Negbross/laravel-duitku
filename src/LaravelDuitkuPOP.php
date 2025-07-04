<?php

namespace Negbross\LaravelDuitku;

use Negbross\LaravelDuitku\Enums\ResponseCode;
use Negbross\LaravelDuitku\Exceptions\DuitkuResponseException;
use Negbross\LaravelDuitku\Exceptions\InvalidSignatureException;
use Negbross\LaravelDuitku\Exceptions\MissingParamaterException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LaravelDuitkuPOP
{
    public string $merchantCode;
    public string $apiKey;
    public string $callbackUrl;
    public string $returnUrl;
    public string $url;
    public string $env;
    public string $datetime;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->merchantCode     = config('duitku.merchant_code');
        $this->apiKey           = config('duitku.api_key');
        $this->callbackUrl      = config('duitku.callback_url');
        $this->returnUrl        = config('duitku.return_url');
        $this->env              = config('duitku.env');
        $this->datetime         = now()->getTimestampMs();

        $this->setUrl();
    }

    /**
     * Set url domain
     *
     * @return void
     */
    public function setUrl(): void
    {
        $this->url = config('duitku.env') === 'production'
            ? 'https://api-prod.duitku.com'
            : 'https://api-sandbox.duitku.com';
    }

    /**
     * Set config
     *
     * @param string $merchantCode
     * @param string $apiKey
     * @return $this
     */
    public function config(string $merchantCode, string $apiKey): static
    {
        $this->merchantCode     = $merchantCode;
        $this->apiKey           = $apiKey;

        return $this;
    }

    /**
     * Create payment transaction
     *
     * @param array $data
     * @return object
     * @throws DuitkuResponseException
     * @throws InvalidSignatureException
     * @throws MissingParamaterException
     * @throws RequestException
     */
    public function createTransaction(array $data): object
    {
        // Validate data input
        $validator = Validator::make($data, [
            'paymentAmount'     => ['required', 'numeric'],
            'merchantOrderId'   => ['required', 'string', 'max:50'],
            'productDetails'    => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255'],
            'additionalParam'   => ['nullable', 'string', 'max:255'],
            'merchantUserInfo'  => ['nullable', 'string', 'max:255'],
            'customerVaName'    => ['nullable', 'string', 'max:20'],
            'phoneNumber'       => ['nullable', 'string', 'max:50'],
            'itemDetails'       => ['nullable', 'array'],
            'customerDetail'    => ['nullable', 'array'],
            'expiryPeriod'      => ['nullable', 'numeric'],
        ]);
        if ($validator->fails()) {
            throw new MissingParamaterException();
        }

        // Request data to API
        $signature = hash('sha256', $this->merchantCode . $this->datetime . $this->apiKey);
        $response = Http::withHeaders([
                'x-duitku-signature' => $signature,
                'x-duitku-timestamp' => $this->datetime,
                'x-duitku-merchantcode' => $this->merchantCode,
            ])->post($this->url.'/api/merchant/createInvoice', array_merge($data, [
                'merchantcode'  => $this->merchantCode,
                'returnUrl'     => $this->returnUrl,
                'callbackUrl'   => $this->callbackUrl,
                'signature'     => $signature,
            ]))->throw(function (Response $response) {
                if (str_contains($response->body(), 'Wrong Signature')) {
                    throw new InvalidSignatureException();
                }

                throw new DuitkuResponseException('Error response code http: '.$response->status().', body: '.$response->body());
            })->object();

        // Return data new transaction
        if ($response && $response->statusCode === ResponseCode::Success) {
            return (object)[
                'success'       => true,
                'merchantCode'  => $response->merchantCode,
                'reference'     => $response->reference,
                'paymentUrl'    => $response->paymentUrl,
                'statusCode'    => $response->statusCode,
                'statusMessage' => $response->statusMessage,
            ];
        }

        return (object)[
            'success'          => false,
            'statusCode'       => $response->statusCode,
            'statusMessage'    => $response->statusMessage,
        ];
    }

    /**
     * Capture callback notification payment
     *
     * @return object
     * @throws InvalidSignatureException
     * @throws RequestException
     */
    public function getNotificationTransaction(): object
    {
        if (isset(request()->merchantCode) && isset(request()->amount) && isset(request()->merchantOrderId) && isset(request()->signature)) {
            $calcSignature = md5(request()->merchantCode . request()->amount . request()->merchantOrderId . $this->apiKey);

            if(request()->signature == $calcSignature)
            {
                return (object) [
                    'merchantCode'      => request()->merchantCode,
                    'amount'            => request()->amount,
                    'merchantOrderId'   => request()->merchantOrderId,
                    'productDetail'     => request()->productDetail,
                    'additionalParam'   => request()->additionalParam,
                    'paymentCode'       => request()->paymentCode,
                    'resultCode'        => request()->resultCode,
                    'merchantUserId'    => request()->merchantUserId,
                    'reference'         => request()->reference,
                    'publisherOrderId'  => request()->publisherOrderId,
                    'spUserHash'        => request()->spUserHash,
                    'settlementDate'    => request()->settlementDate,
                    'issuerCode'        => request()->issuerCode,
                ];
            }

            throw new InvalidSignatureException('Bad Signature');
        }

        throw new InvalidSignatureException('Bad Parameter');
    }
}
