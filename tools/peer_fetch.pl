#!/usr/bin/perl -w

use strict;

use DBI;
use LWP::Simple;
use XML::DOM;

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

my $s = $db->prepare("select * from peers");
$s->execute;
while (my $a = $s->fetchrow_hashref) {
    print "$$a{tag}\n";
    my $users = fetch($$a{tag}, $$a{url});
    if ($users) {
        my $t = $db->prepare("update peers set users = ?, last_contact = current_timestamp where tag = ?");
        $t->execute($users, $$a{tag});
    }
}
$db->disconnect;

sub fetch {
    my ($tag, $url) = @_;
    my $xml = get("$url/top.php");
    my $parser = new XML::DOM::Parser;
    my $top = $parser->parse($xml);
    my $s = $db->prepare("delete from peer_pilot where peer_tag = ?");
    $s->execute($tag);
    my $n = 0;
    foreach my $pilot (@{$top->getDocumentElement->getElementsByTagName('pilots')}[0]->getElementsByTagName('pilot')) {
        my $s = $db->prepare("insert into peer_pilot (peer_tag, username, hours) values (?, ?, ?)");
        $s->execute($tag, $pilot->getAttribute('name'), $pilot->getAttribute('hours'));
        $n++;
    }
    return $n;
}
