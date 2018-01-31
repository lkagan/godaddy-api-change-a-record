<?php
/**
 * Quick and dirty utility to update a bunch of domains to a single IP on GoDaddy DNS
 *
 * Expects a domains.txt file in the current directory with one domain per line.
 */

declare(strict_types = 1);
namespace Superiocity\GoDaddyAPIChangeARecord;

require __DIR__ . '/vendor/autoload.php';
require 'config.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;

if (! $logfile_handler = fopen(__DIR__. '/log-' . date('Y-m-d') . '.txt', 'a')) {
    exit('Can\'t open log file.' . PHP_EOL);
}

$body = \GuzzleHTTP\json_encode([
    'name' => '@',
    'data' => $config[ENV]['new_ip'],
]);

$client = new Client([
    'base_uri' => $config[ ENV ]['uri'],
    'headers' => [
        'Authorization' => "sso-key {$config[ENV]['key']}:{$config[ENV]['secret']}",
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json',
    ],
    'body' => $body,
]);


$domains = get_domains();

foreach ($domains as $domain) {
    if (empty($domain)) {
        continue;
    }

    $endpoint = sprintf('domains/%s/records/A', trim($domain));

    try {
        sleep(3);
        $response = $client->request('PUT', $endpoint);

        if ($response->getStatusCode() === 200) {
            $message = ' Success: ' . $domain  . PHP_EOL;
        } else {
            $message = 'FAIL: ' . $domain  . ' ' . $response->getReasonPhrase();
        }
    } catch (ClientException $e) {
        $message = 'FAIL: ' . $domain  . ' ' . $e->getMessage();
    }

    log($logfile_handler, $message);
}


function get_domains()
{
    $domains = explode("\n", file_get_contents('domains.txt'));
    return $domains;
}


function log($logfile, string $message)
{
    $timestamp = date('Y-m-d h:i:s');
    $message = $timestamp . ' ' . $message . PHP_EOL;
    fwrite($logfile, $message);
}
