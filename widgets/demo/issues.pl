#!/usr/bin/perl
use JSON::XS;
use Data::Dumper;

$widget = {};

$widget->{body} = <<HTML;
<span class="text-XL yellow">11</span>
<span class="text-L">/36</span>
HTML

print encode_json($widget);
