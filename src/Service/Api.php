<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class Api
{
    public const STATUS_CODE = 200;

    public function searchSchool($student, $dist=5000): array
    {
        $lat = $student->getLatitude();
        $lon = $student->getLongitude();

        $etablissement = $student->getCursus()->getEtablissement();
        $elementaire = 'ECOLE DE NIVEAU ELEMENTAIRE';

        if ($etablissement === 'Maternelle'){
            $elementaire = 'ECOLE MATERNELLE';
            $etablissement = 'Ecole';
        }

        $url = "https://data.education.gouv.fr/api/records/1.0/search/"
                ."?dataset=fr-en-annuaire-education&q=&rows=100"
                ."&geofilter.distance=" . $lat . "," . $lon . "," . $dist // Distance par rapport a des coordonnées
                ."&refine.type_etablissement=" . $etablissement  // Collège Lycée Ecole
                ."&refine.etat=OUVERT";

        if($etablissement === 'Ecole') {
            $url = $url."&refine.libelle_nature=" . $elementaire;
        }
        return $this->getResponse($url);
    }

    public function localize($completCity): array
    {
        if ($completCity) {
            $url = "https://nominatim.openstreetmap.org/search?q="
                . $completCity . "&format=json&addressdetails=1&limit=1";
            return $this->getResponse($url);
        } else {
            return [0 => ['lat' => 46.16, 'lon' => 3.19619]];
        }
    }

    public function getResponse(string $url): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $url);
        $statusCode = $response->getStatusCode();
        if ($statusCode === self::STATUS_CODE) {
            // get the response in JSON format
            return $response->toArray();
            // convert the response (here in JSON) to an PHP array
        }
        return ['status' => $response->getStatusCode()];
    }
}
