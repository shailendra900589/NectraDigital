<?php
namespace Growth\Engines;

use Growth\Models\Service;
use Growth\Models\City;
use Growth\Models\LandingPage;

class InternalLinkEngine
{
    public static function generate(array $service, array $city, array $ctx): array
    {
        $links = [];
        $links[] = ['url' => '/', 'title' => 'Home', 'type' => 'core'];
        $links[] = ['url' => '/services', 'title' => 'All Services', 'type' => 'core'];
        $links[] = ['url' => '/contact?city=' . urlencode($city['name']), 'title' => 'Contact Us', 'type' => 'cta'];
        $links[] = ['url' => '/about', 'title' => 'About Nectra Digital', 'type' => 'core'];
        $links[] = ['url' => '/insights', 'title' => 'Blog & Intel', 'type' => 'blog'];
        $links[] = ['url' => '/aeo', 'title' => 'SEO Answers', 'type' => 'core'];

        $services = Service::all(true);
        $count = 0;
        foreach ($services as $s) {
            if ((int)$s['id'] === (int)$service['id']) continue;
            if ($count >= 4) break;
            $slug = ge_build_landing_slug($s['url_prefix'], $city['slug']);
            $links[] = ['url' => '/' . $slug, 'title' => $s['name'] . ' in ' . $city['name'], 'type' => 'service'];
            $count++;
        }

        $cities = City::all(true);
        $cityCount = 0;
        foreach ($cities as $c) {
            if ((int)$c['id'] === (int)$city['id']) continue;
            if ($cityCount >= 4) break;
            $slug = ge_build_landing_slug($service['url_prefix'], $c['slug']);
            $links[] = ['url' => '/' . $slug, 'title' => $service['name'] . ' in ' . $c['name'], 'type' => 'city'];
            $cityCount++;
        }

        $links[] = ['url' => '/contact?service=' . urlencode('Free SEO Audit'), 'title' => 'Get Free Audit', 'type' => 'cta'];
        $links[] = ['url' => '/contact?service=' . urlencode('Consultation'), 'title' => 'Book Consultation', 'type' => 'cta'];

        return $links;
    }
}
