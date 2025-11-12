<?php
namespace MyApp;

final class AllowedSiteConfig
{
    public function allowedSites(): array
    {
        $data = [
            [
                'name' => 'We Connect',
                'domain' => 'mtechsciverse.com',
                'link' => 'http://localhost',
                'apiKey' => '5PcaWKv40oR0zMQwCQE8AoYjuLvJwYe7xOl065BnKLL2zprRADBWI44hGX5lh1I86l1f53utTIqhk2h9jCFSBw==',
                'address' => "10 location, address",
                'phone' => "0938984933"
            ],
            [
                'name' => 'We Connect',
                'domain' => 'mtechsciverse.com',
                'link' => 'http://localhost:8080',
                'apiKey' => '5PcaWKv40oR0zMQwCQE8AoYjuLvJwYe7xOl065BnKLL2zprRADBWI44hGX5lh1I86l1f53utTIqhk2h9jCFSBw==',
                'address' => "10 location, address",
                'phone' => "0938984933"
            ],
            [
                'name' => 'We Connect',
                'domain' => 'mtechsciverse.com',
                'link' => 'https://localhost',
                'apiKey' => '5PcaWKv40oR0zMQwCQE8AoYjuLvJwYe7xOl065BnKLL2zprRADBWI44hGX5lh1I86l1f53utTIqhk2h9jCFSBw==',
                'address' => "10 location, address",
                'phone' => "0938984933"
            ],
            [
                'name' => 'We Connect',
                'domain' => 'mtechsciverse.com',
                'link' => 'http:127.0.0.20:8080',
                'apiKey' => '5PcaWKv40oR0zMQwCQE8AoYjuLvJwYe7xOl065BnKLL2zprRADBWI44hGX5lh1I86l1f53utTIqhk2h9jCFSBw==',
                'address' => "10 location, address",
                'phone' => "0938984933"
            ],
            [
                'name' => 'We Connect',
                'domain' => 'weconnect.com',
                'link' => 'https://weconnect.com',
                'apiKey' => 'GHyJdEA4LtLK9t6HHPWtTZNVIe3kXf1ATt0X5ckxteQs7NLa9KZdalxtGCw2hgNZIOsmSmmLKX6CnaDRjSWrsg==',
                'address' => "10 location, address",
                'phone' => "0938984933"
            ],
        ];
        return $data;
    }
}
