#!/usr/bin/perl -T
#
# $Id$
#
# Script to import a lolbackup.tsv file from the old, single-user version of lol
#
# usage: lol-import.pl username password lolbackup.tsv
#

use strict;
require DBI;
use Digest::MD5 qw(md5_hex);

$ENV{PATH} = '/bin:/usr/bin:/usr/local/bin';

my $schema = 0.00;

my $dbh = DBI->connect("DBI:Pg:dbname=lol", "lolsite");
usage("database connection error") if(!$dbh);

my $sth = $dbh->prepare("select id from pilot where username = '$ARGV[0]' and password = '".md5_hex($ARGV[1])."'");
$sth->execute;
my $ref = $sth->fetchrow_hashref();
my $pilot_id = int $ref->{'id'};
usage("pilot not found") if(!$pilot_id);

open FILE, $ARGV[2] or usage("could not open $ARGV[2]");
while(<FILE>) {
  my $buf = $_;
  chomp($buf);
  $buf =~ s/\'/\\\'/g;

  if($buf =~ /^SCHEMA\t([\d\.]+)$/) {
    $schema = $1;
  } elsif( $buf =~ /^AIRCRAFT\t(.*)$/) {
    craft_aircraft($1);
  } elsif( $buf =~ /^MEDICAL\t(.*)$/) {
    craft_medical($1);
  } elsif( $buf =~ /^LOGBOOK\t(.*)$/) {
    craft_logbook($1);
  } elsif( $buf =~ /^RATINGS\t(.*)$/) {
    craft_ratings($1);
  } elsif( $buf =~ /^CERTIFICATIONS\t(.*)$/) {
    craft_certifications($1);
  } elsif( $buf =~ /^PASSENGERS\t(.*)$/) {
    craft_passengers($1);
  }
}
close FILE;

sub craft_logbook {
  my ($buf) = @_;
  chomp($buf);

  my($date,$ident,$route_from,$route_to,$route_to2,$route_to3,$route_to4,
     $route,$passengers,
     $remarks,$landings_day,$landings_night,$instrument_approach,
     $class_1,$class_2,$flight_training,$conditions_night,
     $conditions_actualinstr,$conditions_simulinstr,
     $type_xc,$type_cfi,$type_dual,$type_pic,$type_sic,
     $detail,$url,$cost);

  if($schema == 0.00) {
    die "Unknown schema version!\n";
  } elsif($schema <= 0.12) {
    ($date,$ident,$route_from,$route_to,$route_to2,$route_to3,$route_to4,
     $remarks,$landings_day,$landings_night,$instrument_approach,
     $class_1,$class_2,$flight_training,$conditions_night,
     $conditions_actualinstr,$conditions_simulinstr,
     $type_xc,$type_cfi,$type_dual,$type_pic,$type_sic,
     $detail,$url)
     = split /\t/, $buf;
     $route = $route_from." ".$route_to." ".$route_to2." ".$route_to3." ".$route_to4;
     $route =~ s/  / /g;

  } elsif($schema <= 0.13) {
    ($date,$ident,$route_from,$route_to,$route_to2,$route_to3,$route_to4,
     $remarks,$landings_day,$landings_night,$instrument_approach,
     $class_1,$class_2,$flight_training,$conditions_night,
     $conditions_actualinstr,$conditions_simulinstr,
     $type_xc,$type_cfi,$type_dual,$type_pic,$type_sic,
     $detail,$url,$cost)
     = split /\t/, $buf;
     $route = $route_from." ".$route_to." ".$route_to2." ".$route_to3." ".$route_to4;
     $route =~ s/  / /g;
  } elsif($schema <= 0.14) {
    ($date,$ident,$route_from,$route_to,$route_to2,$route_to3,$route_to4,
     $remarks,$landings_day,$landings_night,$instrument_approach,
     $conditions_night,
     $conditions_actualinstr,$conditions_simulinstr,
     $type_xc,$type_cfi,$type_dual,$type_pic,$type_sic,
     $detail,$url,$cost)
     = split /\t/, $buf;
     $route = $route_from." ".$route_to." ".$route_to2." ".$route_to3." ".$route_to4;
     $route =~ s/  / /g;
  } else {
    ($date,$ident,$route,$passengers,
     $remarks,$landings_day,$landings_night,$instrument_approach,
     $conditions_night,
     $conditions_actualinstr,$conditions_simulinstr,
     $type_xc,$type_cfi,$type_dual,$type_pic,$type_sic,
     $detail,$url,$cost)
     = split /\t/, $buf;
  }

  $landings_day = int $landings_day;
  $landings_night = int $landings_night;
  $instrument_approach = int $instrument_approach;
  $conditions_night = sprintf("%3.1f",$conditions_night);
  $conditions_actualinstr = sprintf("%3.1f",$conditions_actualinstr);
  $conditions_simulinstr = sprintf("%3.1f",$conditions_simulinstr);
  $type_xc = sprintf("%3.1f",$type_xc);
  $type_cfi = sprintf("%3.1f",$type_cfi);
  $type_dual = sprintf("%3.1f",$type_dual);
  $type_pic = sprintf("%3.1f",$type_pic);
  $type_sic = sprintf("%3.1f",$type_sic);
  $cost = sprintf("%4.1f",$cost);

  dosql("INSERT INTO logbook (
            pilot_id, date, ident, route, passengers,
            remarks, landings_day, landings_night, instrument_approach,
            conditions_night,
            conditions_actualinstr, conditions_simulinstr,
            type_xc, type_cfi, type_dual, type_pic, type_sic,
            detail, url, cost
        ) VALUES " .
        "($pilot_id,'$date','$ident','$route','$passengers'," .
        "'$remarks',$landings_day,$landings_night,$instrument_approach," .
        "$conditions_night," .
        "$conditions_actualinstr,$conditions_simulinstr," .
        "$type_xc,$type_cfi,$type_dual,$type_pic,$type_sic," .
        "'$detail','$url',$cost)");
}

sub craft_aircraft {
  my ($buf) = @_;
  chomp($buf);

  my($ident,$makemodel,$aircraft_class,$complex,$high_perf,$retract,$tailwheel,$image_url,
     $link_url,$detail,$home_field);

  if($schema == 0.00) {
    die "Unknown schema version!\n";
  } elsif($schema <= 0.12) {
    ($ident,
     $makemodel,
     $aircraft_class) 
     = split /\t/, $buf;
  } elsif($schema <= 0.14) {
    ($ident,
     $makemodel,
     $aircraft_class,
     $complex,
     $high_perf,
     $retract,
     $tailwheel,
     $image_url,
     $link_url,
     $detail)
     = split /\t/, $buf;
  } else {
    ($ident,
     $makemodel,
     $aircraft_class,
     $complex,
     $high_perf,
     $retract,
     $tailwheel,
     $home_field,
     $image_url,
     $link_url,
     $detail)
     = split /\t/, $buf;
  }

  $complex = int $complex ? 'true' : 'false';
  $high_perf = int $high_perf ? 'true' : 'false';
  $retract = int $retract ? 'true' : 'false';
  $tailwheel = int $tailwheel ? 'true' : 'false';

  dosql("INSERT INTO aircraft (ident, pilot_id, makemodel, aircraft_class, complex, high_perf, tailwheel, home_field, image_url, link_url, detail) VALUES ('$ident',$pilot_id,'$makemodel',$aircraft_class,$complex,$high_perf,$tailwheel,'$home_field','$image_url','$link_url','$detail')");
}

sub craft_medical {
  my ($buf) = @_;
  chomp($buf);

  my($date,$class,$name);

  if($schema == 0.00) {
    die "Unknown schema version!\n";
  } else {
    ($date,
     $class,
     $name) 
     = split /\t/, $buf;
  }

  dosql("INSERT INTO medical (pilot_id, date, class, name) VALUES ($pilot_id,'$date','$class','$name')");
}

sub craft_ratings {
  my ($buf) = @_;
  chomp($buf);

  my($name,$issued);

  if($schema == 0.00) {
    die "Unknown schema version!\n";
  } else {
    ($name,
     $issued)
     = split /\t/, $buf;
  }

  dosql("INSERT INTO ratings (pilot_id, name, issued) VALUES ($pilot_id,'$name','$issued')");
}

sub craft_certifications {
  my ($buf) = @_;
  chomp($buf);

  my($name,$issued,$certificate);

  if($schema == 0.00) {
    die "Unknown schema version!\n";
  } else {
    ($name,
     $issued,
     $certificate)
     = split /\t/, $buf;
  }

  dosql("INSERT INTO certifications (pilot_id, name, issued, details) VALUES ($pilot_id,'$name','$issued','$certificate')");
}

sub craft_passengers {
  my ($buf) = @_;
  chomp($buf);

  my($alias,$fullname,$image_url,$link_url,$detail);

  if($schema == 0.00) {
    die "Unknown schema version!\n";
  } else {
    ($alias,
     $fullname,
     $image_url,
     $link_url,
     $detail)
     = split /\t/, $buf;
  }

  dosql("INSERT INTO passengers (alias, pilot_id, fullname, image_url, link_url, detail) VALUES ('$alias',$pilot_id,'$fullname','$image_url','$link_url','$detail')");
}

sub dosql {
  my ($buf) = @_;

  print "$buf\n";
  $dbh->do($buf) or die "SQL Failed";
}
sub usage {
  print "Script to import a lolbackup.tsv file from the old, single-user version of lol\n\n";
  print "usage: lol-import.pl username password lolbackup.tsv\n\n";
  print "error: $_[0]\n" if $_[0];
  exit;
}
