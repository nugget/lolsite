#!/usr/bin/perl -w

# This is used to upgrade from schema 3 to schema 4.
# Change the DBI->connect line as appropriate.

use DBI;

my $dbtype = 'mysql';

my $db = DBI->connect("DBI:Pg:dbname=lol", "lolsite") if($dbtype eq 'psql');
my $db = DBI->connect("DBI:mysql:lolsite;host=localhost","lolsite","lspasswd") if($dbtype eq 'mysql');

die unless $db;

$s = $db->prepare("select id, route from logbook");
$s->execute;
while (@a = $s->fetchrow) {
    $route = $a[1];
    print "$route\n";
    @r = split /\s+/, $route;
    $t = $db->prepare("delete from flight_route where logbook_id = ?");
    $t->execute($a[0]);
    $seq = 0;
    foreach (@r) {
        $t = $db->prepare("insert into flight_route (logbook_id, airport, sequence) values (?,?,?)");
        $t->execute($a[0], $_, $seq++);
    }
}
$db->disconnect;
