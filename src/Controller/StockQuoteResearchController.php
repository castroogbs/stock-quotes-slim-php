<?php

declare(strict_types=1);

namespace App\Controller;

use stdClass;
use Exception;
use GuzzleHttp\Client;
use App\Security\JWTUtil;
use OpenApi\Attributes as OA;
use App\Model\StockQuoteResearch;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer; 
use App\Service\StockQuoteResearchService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StockQuoteResearchController
{

     /**
     * @var StockQuoteResearchService $service
     */
    protected StockQuoteResearchService $service;

    /**
     * @var JWTUtil $jwtUtil
     */
    protected JWTUtil $jwtUtil;
    
    /**
     * @var Mailer $mailer
     */
    protected Mailer $mailer;

    /**
     * @param StockQuoteResearchService $service
     * @param JWTUtil $jwtUtil
     * @param Mailer $mailer
     */
    public function __construct(StockQuoteResearchService $service, JWTUtil $jwtUtil, Mailer $mailer) {
        $this->service = $service;
        $this->jwtUtil = $jwtUtil;
        $this->mailer = $mailer;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    #[OA\Get(path: '/stock', tags: ["StockQuote"])]
    #[OA\Response(response: '200', description: 'Received stock quote, saved register on DB and sent email.')]
    #[OA\Response(response: '400', description: 'Quote symbol cannot be empty.')]
    #[OA\Response(response: '404', description: 'Quote symbol not found. Please try again.')]
    #[OA\Response(response: '500', description: 'Error while calling Stooq API or sendind e-mail.')]
    public function getInfoByStockCode(Request $request, Response $response, array $args): Response {
        $paramValues = $request->getQueryParams();
        $quoteSymbol = $paramValues['q'];

        $authHeader = $request->getHeader('Authorization');
        $token = explode('Bearer ', $authHeader[0])[1];

        $tokenPayload = $this->jwtUtil->getTokenPayload($token);
        $userId = intval($tokenPayload['uid']);

        if(empty($quoteSymbol)) {
            $errorResponse = $response->withStatus(400)
                        ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write( json_encode([ "error" => "Quote symbol cannot be empty."] ) );
            return $errorResponse;
        }
        
        $currentStockQuote = $this->getCurrentStockQuote($quoteSymbol);

        if(!isset($currentStockQuote->stockQuote)) {
            $errorResponse = $response->withStatus(500)
                        ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write( json_encode([ "error" => "Error while calling Stooq API."] ) );
            return $errorResponse;
        }

        if(!isset($currentStockQuote->stockQuote->name)) {
            $errorResponse = $response->withStatus(404)
                        ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write( json_encode([ "error" => "Quote symbol not found. Please try again."] ) );
            return $errorResponse;
        }

        $stockQuote = $currentStockQuote->stockQuote;
        $research = $this->saveResearchRegister($userId, $stockQuote);
        $email = $research->getUser()->getEmail();

        $isEmailSent = $this->sendMail($email, $stockQuote);

        if($isEmailSent !== true) {
            $errorResponse = $response->withStatus(500)
                                    ->withHeader('Content-Type', 'application/json');
            $errorResponse->getBody()->write( json_encode([ "error" => "Error while trying to send mail."]) );
            return $errorResponse;
        }

        $payload = json_encode([
            'name' => $stockQuote->name,
            'symbol' => $stockQuote->symbol,
            'open' => $stockQuote->open,
            'high' => $stockQuote->high,
            'low' => $stockQuote->low,
            'close' => $stockQuote->close
        ]);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json');

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    #[OA\Get(path: '/history', tags: ["StockQuote"])]
    #[OA\Response(response: '200', description: 'Successfully accessed history!')]
    public function getHistory(Request $request, Response $response, array $args): Response {
        $authHeader = $request->getHeader('Authorization');
        $token = explode('Bearer ', $authHeader[0])[1];

        $tokenPayload = $this->jwtUtil->getTokenPayload($token);
        $userId = intval($tokenPayload['uid']);

        $researches = $this->service->findByUserId($userId);

        $history = [];
        foreach ($researches as $research) {
            $register['date'] = $research['date']->format('Y-m-d H:i:s');
            $register['name'] = $research['name'];
            $register['symbol'] = $research['symbol'];
            $register['open'] = $research['open'];
            $register['high'] = $research['high'];
            $register['low'] = $research['low'];
            $register['close'] = $research['close'];

            array_push($history, $register);
        }

        $payload = json_encode($history);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $symbol
     * @return stdClass | string
     */
    private function getCurrentStockQuote(string $symbol): stdClass | string {
        try {
            $client = new Client();
            $clientRes = $client->request('GET', "https://stooq.com/q/l/?s=$symbol&f=sd2t2ohlcvn&e=json");
            $stockQuote = (object) json_decode(
                $clientRes->getBody()->__toString(),
                true
            )['symbols'][0];
            
            return (object) [ 'stockQuote' => $stockQuote ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param int $userId
     * @param stdClass $stockQuote
     * @return StockQuoteResearch
     */
    private function saveResearchRegister(int $userId, stdClass $stockQuote): StockQuoteResearch {
        $newStockQuoteResearchRegister = (new StockQuoteResearch)
                                                ->setUserId($userId)
                                                ->setName($stockQuote->name)
                                                ->setSymbol($stockQuote->symbol)
                                                ->setOpen($stockQuote->open)
                                                ->setHigh($stockQuote->high)
                                                ->setLow($stockQuote->low)
                                                ->setClose($stockQuote->close);
        return $this->service->save($newStockQuoteResearchRegister);
    }

     /**
     * @param string $userEmail
     * @param stdClass $research
     * @return bool | string
     */
    private function sendMail(string $userEmail, stdClass $research): bool | string {
        try {
            $email = (new Email())
            ->from('gabriel@phpchallenge.com')
            ->to($userEmail)
            ->subject('Stock quote research result.')
            // ->text('')
            ->html("
                <h1>Here are your latest stock quote search results: </h1>
                <table>
                    <tr>
                        <td colspan=2>Stock Quote Results:</td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>$research->name</td>
                    </tr>
                    <tr>
                        <td>Symbol</td>
                        <td>$research->symbol</td>
                    </tr>
                    <tr>
                        <td>Open</td>
                        <td>$research->open</td>
                    </tr>
                    <tr>
                        <td>High</td>
                        <td>$research->high</td>
                    </tr>
                    <tr>
                        <td>Low</td>
                        <td>$research->low</td>
                    </tr>
                    <tr>
                        <td>Close</td>
                        <td>$research->close</td>
                    </tr>
                </table>
            ");
            $this->mailer->send($email);
            return true;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}

?>