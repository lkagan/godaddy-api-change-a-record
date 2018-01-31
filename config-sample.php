<?php
declare(strict_types = 1);
namespace Superiocity\GoDaddyAPIChangeARecord;

$config = [];

const ENV = 'prod'; // dev | prod

$config['dev'] = [
    'uri'    => 'https://api.ote-godaddy.com/v1/',
    'key'    => '',
    'secret' => '',
    'new_ip' => '45.55.189.222',
];

$config['prod'] = [
    'uri'    => 'https://api.godaddy.com/v1/',
    'key'    => '',
    'secret' => '',
    'new_ip' => '45.55.189.222',
];
