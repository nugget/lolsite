<?php
    include "include/init.inc";

    pilot_id_redirect($rvar_pilot);
    $rvar_pilot = pilot_lookup($rvar_pilot);

    if (!isset($rvar_limit)) {
        $rvar_limit = 16;
    }

    $whereclause = "pilot_id = $rvar_pilot";
    $entries = logbook_entries($whereclause);

    $pilot_name = pilot_name($rvar_pilot);

    $headers = apache_request_headers();
    $host = $headers['Host'];

    header("Content-type: text/xml");
    print "<?xml version=\"1.0\"?>\n";
?>
<rss version="2.0">
    <channel>
        <title><?php echo $pilot_name ?>'s logbook</title>
        <link>http://lol.hewgill.com/display_logbook.php?pilot=<?php echo $pilot_name ?></link>
        <description>Logbook for <?php echo $pilot_name ?></description>
<?php
    $n = 0;
    for ($i = count($entries)-1; $i >= 0 && $n < $rvar_limit; $i--, $n++) {
        $line = logbook_detail($entries[$i]);
        echo "<item>\n";
        echo "<title>$line[route]";
        if ($line['passengers']) {
            print "with $line[passengers]";
        } else {
            print "solo";
        }
        print "</title>\n";
        echo "<link>http://$host$GLOBALS[baseurl]/detail_logbook.php?id=$line[id]</link>\n";
        echo "<pubDate>".date("r", strtotime($line['date']))."</pubDate>\n";
        echo "<description>\n";
        echo "Remarks: ".xml_escape($line['remarks'])."<br /><br />\n";
        echo xml_escape($line['detail']);
        echo "</description>\n";
        echo "</item>\n";
    }
?>
    </channel>
</rss>

