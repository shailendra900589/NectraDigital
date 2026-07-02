<?php
/**
 * Unique local copy blocks for service×city pages (reduces programmatic duplicate content).
 */
require_once __DIR__ . '/growth/helpers.php';

function ge_service_city_unique_block(string $serviceSlug, string $citySlug, array $city, array $service): string
{
    $cityName = $city['name'] ?? 'your city';
    $state = $city['state'] ?? 'India';
    $silo = $service['silo'] ?? ($service['h1'] ?? 'Digital Marketing');
    $seed = crc32($serviceSlug . ':' . $citySlug);

    $openers = [
        "Businesses in {$cityName} compete in a market where buyers compare agencies online before they ever pick up the phone.",
        "{$cityName} companies are investing more in digital channels as local competition and search demand continue to rise.",
        "For brands operating in {$cityName}, {$state}, visibility on Google and social platforms directly affects lead quality and sales velocity.",
        "Growth teams in {$cityName} need partners who understand regional buyer behavior—not generic playbooks copied from other metros.",
    ];

    $angles = [
        "We start with a focused audit of your current {$silo} performance, competitor landscape in {$cityName}, and the keywords or audiences that already drive revenue.",
        "Our {$cityName} delivery model pairs a dedicated strategist with channel specialists so campaigns stay aligned with your sales cycle and local seasonality.",
        "Every recommendation is tied to measurable KPIs—qualified leads, cost per acquisition, or pipeline value—so you can see progress week over week.",
        "We combine on-page improvements, campaign structure, and conversion paths so traffic from {$cityName} and surrounding areas converts at a higher rate.",
    ];

    $proof = [
        "Recent engagements include multi-location brands scaling across {$state} and SMBs in {$cityName} that needed faster lead flow without increasing wasted ad spend.",
        "Clients choose us when prior agencies reported clicks and impressions but could not explain which activities produced booked meetings or closed deals.",
        "We document baselines, run structured tests, and share plain-language reports—so stakeholders in {$cityName} always know what changed and why.",
        "When your team needs development or automation support, our software and AI specialists integrate with the same account plan—no handoffs to another vendor.",
    ];

    $o = $openers[$seed % count($openers)];
    $a = $angles[($seed >> 3) % count($angles)];
    $p = $proof[($seed >> 6) % count($proof)];

    return trim("{$o} {$a} {$p}");
}
