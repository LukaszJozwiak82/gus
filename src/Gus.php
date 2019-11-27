<?php

namespace Ljozwiak\Gus;

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\ReportTypes;
use Modules\ClientGenerator\Services\GetData;
use Validator, Input, Redirect;

class Gus
{
    private $gus;

    public function __construct()
    {
        $this->gus = new GusApi(
            config('gus.gus_key'),'prod'
        );
    }
    public function getGusInfo($number)
    {
        try {
            $this->gus->login();
            $numberToCheck = preg_replace("/-/","",$number);
            $regNip = '/^[0-9]{10}$/';
            $regRegon = '/^[0-9]{9}$/';

            if(preg_match($regNip, $numberToCheck)){
                $gusReports = $this->gus->getByNip($numberToCheck);
            }elseif(preg_match($regRegon, $numberToCheck)){
                $gusReports = $this->gus->getByRegon($numberToCheck);
            }else{
                return Redirect::back()->withErrors(['Niepoprawny numer']);
            }

            foreach ($gusReports as $gusReport) {
                //you can change report type to other one
                $reportType = $gusReport->getType();
                $method = 'type' . ucfirst($reportType);
                $reportTypeName = $this->$method($gusReport->getSilo());
                $fullReport = $this->gus->getFullReport($gusReport, $reportTypeName);
                $info = [];
                $prefix = '';
                $type = strtolower($gusReport->getType());

//                dd($fullReport);
//                die();

                $fields = [
                    'adSiedzGmina_Symbol' => 'AdsSymbolGminy',
                    'adSiedzMiejscowosc_Symbol' => 'AdsSymbolMiejscowosci',
                    'adSiedzPowiat_Symbol' => 'AdsSymbolPowiatu',
                    'adSiedzUlica_Symbol' => 'AdsSymbolUlicy',
                    'adSiedzWojewodztwo_Symbol' => 'AdsSymbolWojewodztwa',
                    'podstawowaFormaPrawna_Nazwa' => 'FormaPrawna',
                    'nazwa' => 'NazwaPodmiotu',
                    'nip' => 'Nip',
                    'numerWRejestrzeEwidencji' => 'NumerwRejestrzeLubEwidencji',
                    'organRejestrowy_Nazwa' => 'OrganRejestrowy',
                    'rodzajRejestruEwidencji_Nazwa' => 'RodzajRejestru',
                    'adSiedzNumerNieruchomosci' => 'NumerNieruchomosci',
                ];

                if ($type === "p") {
                    $prefix = 'praw';
                    $fields['regon14'] = 'regon14';
                    $fields['regon9'] = 'regon9';
                } elseif ($type === "f") {
                    $prefix = 'fiz';
                    $info['company_type'] = 2;
                    $fields['regon9'] = 'regon9';
                    $fields['regon14'] = 'regon14';
                    try {
                        $physicReport = $this->gus->getFullReport(
                            $gusReport,
                            ReportTypes::REPORT_ACTIVITY_PHYSIC_PERSON
                        );
                        $fieldsPhysic['imie1'] = 'firstname';
                        $fieldsPhysic['nazwisko'] = 'lastname';
                    } catch (\Exception $e) {
                        echo $e;
                    }
                    foreach ($fieldsPhysic as $key => $value) {
                        $field = $prefix . '_' . $key;
                        $info[$value] = (!empty($physicReport[0][$field])) ? $physicReport[0][$field] : null;
                    }
                }
                //Utworzenie tablicy z polami
                foreach ($fields as $key => $value) {
                    $field = $prefix . '_' . $key;
                    $info[$value] = (!empty($fullReport[0][$field])) ? $fullReport[0][$field] : null;
                }

//                dd($info);
                return '<pre>'. json_encode(['jestWojPowGmnMiej' => true,'pParametryWyszukiwania'=>[$info]]) .'<pre>';
            }
        } catch (InvalidUserKeyException $e) {
            echo 'Bad user key';
        } catch (NotFoundException $e) {
            echo 'No data found <br>';
            echo 'For more information read server message below: <br>';
            echo $this->gus->getResultSearchMessage();
        }
    }

    protected function typeP(int $silo): string
    {
        return ReportTypes::REPORT_PUBLIC_LAW;
    }

    /**
     * @param int $silo
     *
     * @return string
     * @throws InvalidSiloTypeException
     *
     */
    protected function typeF(int $silo): string
    {
        $siloMapper = [
            1 => ReportTypes::REPORT_ACTIVITY_PHYSIC_CEIDG,
            2 => ReportTypes::REPORT_ACTIVITY_PHYSIC_AGRO,
            3 => ReportTypes::REPORT_ACTIVITY_PHYSIC_OTHER_PUBLIC,
            4 => ReportTypes::REPORT_ACTIVITY_LOCAL_PHYSIC_WKR_PUBLIC,
        ];

        if (!array_key_exists($silo, $siloMapper)) {
            throw new InvalidSiloTypeException(sprintf('Invalid silo type: %s', $silo));
        }

        return $siloMapper[$silo];
    }

    /**
     * @param int $silo
     *
     * @return string
     */
    protected function typeLp(int $silo): string
    {
        return ReportTypes::REPORT_LOCAL_LAW_PUBLIC;
    }

    /**
     * @param int $silo
     *
     * @return string
     */
    protected function typeLf(int $silo): string
    {
        return ReportTypes::REPORT_LOCAL_PHYSIC_PUBLIC;
    }
}
