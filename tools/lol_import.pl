#!/usr/bin/perl -w

use strict;

use Data::Dumper;
use DBI;
use XML::DOM;

use vars qw(%pilot);

my $dbtype = 'pgsql';

my $db;
if ($dbtype eq 'pgsql') {
    $db = DBI->connect("DBI:Pg:dbname=lol", "lolsite");
} elsif ($dbtype eq 'mysql') {
    $db = DBI->connect("DBI:mysql:lolsite;host=localhost", "lolsite", "lspasswd");
} else {
    die "Unrecognized dbtype $dbtype";
}
if (!$db) {
    die "Database connection failure";
}

my $parser = new XML::DOM::Parser;
my $dom = $parser->parsefile($ARGV[0]);

my $nodelist = $dom->getDocumentElement->getElementsByTagName('settings');
if (@$nodelist) {
    my $node = $$nodelist[0];
    print "Importing settings\n";
    my $s = $db->prepare("delete from settings");
    $s->execute;
    foreach my $n ($node->getElementsByTagName('setting')) {
        $s = $db->prepare("insert into settings (setting, value) values (?, ?)");
        $s->execute($n->getAttribute('name'), $n->getAttribute('value'));
    }
}

$nodelist = $dom->getDocumentElement->getElementsByTagName('pilots');
if (@$nodelist) {
    my $node = $$nodelist[0];
    print "Importing pilots\n";
    foreach my $n ($node->getElementsByTagName('pilot')) {
        my $name = $n->getAttribute('name');
        print "  $name\n";
        my $s = $db->prepare("delete from pilot where username = ?");
        $s->execute($name);
        my @values;
        foreach my $a ($n->getChildNodes) {
            next unless $a->getNodeType == XML::DOM::ELEMENT_NODE;
            next if $a->getTagName =~ /^(certification|rating|medical|passenger|entry)$/;
            push @values, [$a->getTagName, $a->getFirstChild ? $a->getFirstChild->getNodeValue : ''];
        }
        $s = $db->prepare("insert into pilot (username,".(join ',', map $$_[0], @values).") values (?,".(join ',', map '?', @values).")");
        $s->execute($name, (map $$_[1], @values));
        $s = $db->prepare("select id from pilot where username = ?");
        $s->execute($name);
        my $id = $s->fetchrow_hashref->{id};

        foreach my $c ($n->getElementsByTagName('certification')) {
            $s = $db->prepare("insert into certifications (pilot_id, name, issued, details) values (?, ?, ?, ?)");
            $s->execute($id, $c->getAttribute('name'), $c->getAttribute('issued'), $c->getAttribute('details'));
        }

        foreach my $r ($n->getElementsByTagName('rating')) {
            $s = $db->prepare("insert into ratings (pilot_id, name, issued) values (?, ?, ?)");
            $s->execute($id, $r->getAttribute('name'), $r->getAttribute('issued'));
        }

        foreach my $m ($n->getElementsByTagName('medical')) {
            $s = $db->prepare("insert into medical (pilot_id, date, class, name) values (?, ?, ?, ?)");
            $s->execute($id, $m->getAttribute('date'), $m->getAttribute('class'), $m->getAttribute('name'));
        }

        foreach my $p ($n->getElementsByTagName('passenger')) {
            $s = $db->prepare("insert into passengers (pilot_id, alias, fullname, image_url, link_url, detail) values (?, ?, ?, ?, ?, ?)");
            $s->execute($id, $p->getAttribute('alias'), $p->getAttribute('fullname'), $p->getAttribute('image_url'), $p->getAttribute('link_url'), $p->getAttribute('detail'));
        }

        foreach my $e ($n->getElementsByTagName('entry')) {
            my @values;
            foreach my $l ($e->getChildNodes) {
                next unless $l->getNodeType == XML::DOM::ELEMENT_NODE;
                next if $l->getTagName =~ /^(route)$/;
                push @values, [$l->getTagName, $l->getFirstChild ? $l->getFirstChild->getNodeValue : ''];
            }
            $s = $db->prepare("insert into logbook (pilot_id,".(join ',', map $$_[0], @values).") values (?,".(join ',', map '?', @values).")");
            $s->execute($id, (map $$_[1], @values));
            $s = $db->prepare("select id from logbook where pilot_id = ? order by id desc limit 1");
            $s->execute($id);
            my $logbook_id = $s->fetchrow_hashref->{id};
            my $seq = 0;
            foreach my $r ($e->getElementsByTagName('route')->[0]->getElementsByTagName('airport')) {
                $s = $db->prepare("insert into flight_route (logbook_id, airport, sequence) values (?, ?, ?)");
                $s->execute($logbook_id, $r->getFirstChild->getNodeValue, $seq++);
            }
        }

    }
}

$nodelist = $dom->getDocumentElement->getElementsByTagName('aircraft');
if (@$nodelist) {
    my $node = $$nodelist[0];
    print "Importing aircraft\n";
    foreach my $n ($node->getElementsByTagName('aircraft')) {
        my $ident = $n->getAttribute('ident');
        print "  $ident\n";
        my $s = $db->prepare("delete from aircraft where ident = ?");
        $s->execute($ident);
        my @values;
        foreach my $a ($n->getChildNodes) {
            next unless $a->getNodeType == XML::DOM::ELEMENT_NODE;
            next if $a->getTagName =~ /^(comment)$/;
            push @values, [$a->getTagName, $a->getFirstChild ? $a->getFirstChild->getNodeValue : ''];
        }
        $s = $db->prepare("insert into aircraft (ident,".(join ',', map $$_[0], @values).") values (?,".(join ',', map '?', @values).")");
        $s->execute($ident, (map $$_[1], @values));
    }
}

$nodelist = $dom->getDocumentElement->getElementsByTagName('airports');
if (@$nodelist) {
    my $node = $$nodelist[0];
    print "Importing airports\n";
    foreach my $n ($node->getElementsByTagName('airport')) {
        my $ident = $n->getAttribute('ident');
        print "  $ident\n";
        my $s = $db->prepare("delete from airports where ident = ?");
        $s->execute($ident);
        my @values;
        foreach my $a ($n->getChildNodes) {
            next unless $a->getNodeType == XML::DOM::ELEMENT_NODE;
            next if $a->getTagName =~ /^(comment)$/;
            push @values, [$a->getTagName, $a->getFirstChild ? $a->getFirstChild->getNodeValue : ''];
        }
        $s = $db->prepare("insert into airports (ident,pilot_id,".(join ',', map $$_[0], @values).") values (?,?,".(join ',', map '?', @values).")");
        $s->execute($ident, 0, (map $$_[1], @values));
    }
}

$db->disconnect;
