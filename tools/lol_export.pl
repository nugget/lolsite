#!/usr/bin/perl -w

use strict;

use DBI;

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

my $s = $db->prepare("select id, username from pilot");
$s->execute;
while (my $a = $s->fetchrow_hashref) {
    $pilot{$$a{id}} = $$a{username};
}

print "<?xml version=\"1.0\"?>\n";
print "<lol>\n";
dump_settings($db);
#dump_peers($db);
dump_pilots($db);
dump_aircraft($db);
dump_airports($db);
print "</lol>\n";
$db->disconnect;

sub dump_settings {
    my $db = $_[0];
    print "  <settings>\n";
    my $s = $db->prepare("select * from settings");
    $s->execute;
    while (my $a = $s->fetchrow_hashref) {
        print "    <setting name=\"$$a{setting}\" value=\"$$a{value}\" />\n";
    }
    print "  </settings>\n";
}

sub dump_pilots {
    my $db = $_[0];
    print "  <pilots>\n";
    my $s = $db->prepare("select * from pilot");
    $s->execute;
    while (my $p = $s->fetchrow_hashref) {
        print "    <pilot name=\"$$p{username}\">\n";
        foreach (qw(fullname server_tag password displayname publish_local publish_global admin email dob image_url link_url added last_login)) {
            print "      <$_>".escape($$p{$_})."</$_>\n" if defined $$p{$_};
        }
        my $t = $db->prepare("select * from certifications where pilot_id = ?");
        $t->execute($$p{id});
        while (my $c = $t->fetchrow_hashref) {
            print "      <certification name=\"$$c{name}\" issued=\"$$c{issued}\" details=\"$$c{details}\" />\n";
        }
        $t = $db->prepare("select * from ratings where pilot_id = ?");
        $t->execute($$p{id});
        while (my $r = $t->fetchrow_hashref) {
            print "      <rating name=\"$$r{name}\" issued=\"$$r{issued}\" />\n";
        }
        $t = $db->prepare("select * from medical where pilot_id = ?");
        $t->execute($$p{id});
        while (my $m = $t->fetchrow_hashref) {
            print "      <medical date=\"$$m{date}\" class=\"$$m{class}\" name=\"$$m{name}\" />\n";
        }
        $t = $db->prepare("select * from passengers where pilot_id = ?");
        $t->execute($$p{id});
        while (my $b = $t->fetchrow_hashref) {
            print "      <passenger alias=\"$$b{alias}\" fullname=\"$$b{fullname}\" image_url=\"$$b{image_url}\" link_url=\"$$b{link_url}\" detail=\"$$b{detail}\" />\n";
        }
        $t = $db->prepare("select * from logbook where pilot_id = ? order by id");
        $t->execute($$p{id});
        while (my $l = $t->fetchrow_hashref) {
            print "      <entry>\n";
            foreach (qw(date ident passengers remarks landings_day landings_night instrument_approach conditions_night conditions_actualinstr conditions_simulinstr type_xc type_cfi type_dual type_pic type_sic launch_type alt_release alt_maximum detail url cost)) {
                print "        <$_>".escape($$l{$_})."</$_>\n" if defined $$l{$_};
            }
            print "        <route>\n";
            my $r = $db->prepare("select * from flight_route where logbook_id = ? order by sequence");
            $r->execute($$l{id});
            while (my $a = $r->fetchrow_hashref) {
                print "          <airport>$$a{airport}</airport>\n";
            }
            print "        </route>\n";
            print "      </entry>\n";
        }
        print "    </pilot>\n";
    }
    print "  </pilots>\n";
}

sub dump_aircraft {
    my $db = $_[0];
    print "  <aircraft>\n";
    my $s = $db->prepare("select * from aircraft");
    $s->execute;
    while (my $a = $s->fetchrow_hashref) {
        print "    <aircraft ident=\"$$a{ident}\">\n";
        foreach (qw(makemodel aircraft_class complex high_perf tailwheel home_field image_url link_url detail)) {
            print "      <$_>".escape($$a{$_})."</$_>\n" if defined $$a{$_};
        }
        my $t = $db->prepare("select * from aircraft_comments where aircraft_ident = ?");
        $t->execute($$a{ident});
        while (my $c = $t->fetchrow_hashref) {
            print "      <comment pilot=\"$pilot{$$c{pilot_id}}\" date=\"$$c{date}\" global=\"$$c{global}\" private=\"$$c{private}\">$$c{detail}</comment>\n";
        }
        print "    </aircraft>\n";
    }
    print "  </aircraft>\n";
}

sub dump_airports {
    my $db = $_[0];
    print "  <airports>\n";
    my $s = $db->prepare("select * from airports");
    $s->execute;
    while (my $a = $s->fetchrow_hashref) {
        print "    <airport ident=\"$$a{ident}\">\n";
        foreach (qw(fullname airspace city timezone tower image_url link_url detail)) {
            print "      <$_>".escape($$a{$_})."</$_>\n" if defined $$a{$_};
        }
        my $t = $db->prepare("select * from airport_comments where airport_ident = ?");
        $t->execute($$a{ident});
        while (my $c = $t->fetchrow_hashref) {
            print "      <comment pilot=\"$pilot{$$c{pilot_id}}\" date=\"$$c{date}\" global=\"$$c{global}\" private=\"$$c{private}\">$$c{detail}</comment>\n";
        }
        print "    </airport>\n";
    }
    print "  </airports>\n";
}

sub escape {
    local $_ = $_[0];
    s/&/&amp;/g;
    s/</&lt;/g;
    return $_;
}
