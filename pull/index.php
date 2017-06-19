<?php
    include('../includes/config.php');

    function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    $sId = mysql_real_escape_string($_GET['url']);
    if (is_numeric($sId)) {
        $data = mysql_query("SELECT * FROM servers WHERE id='$sId'");
        $result = mysql_fetch_array($data);
        $url = $result['url']."/uptime.php";
        $output = get_data($url);
        if (($output == NULL) || ($output === false)) {
            $array = array();
            $array['uptime'] = '
		<div class="progress">
			<div class="bar bar-danger" style="width: 100%;"><small>Down</small></div>
		</div>
		';
            $array['load'] = '
		<div class="progress">
			<div class="bar bar-danger" style="width: 100%;"><small>Down</small></div>
		</div>
		';
            $array['online'] = '
		<div class="progress">
			<div class="bar bar-danger" style="width: 100%;"><small>Down</small></div>
		</div>
		';
            echo json_encode($array);
        } else {
            $data = json_decode($output, true);
            if (0 == $memory = $data['memory']) {
                $memlevel = null;
            } elseif ($memory >= 51) {
                $memlevel = "success";
            } elseif ($memory <= 50) {
                $memlevel = "warning";
            } elseif ($memory <= 35) {
                $memlevel = "danger";
            }

            $data['memory'] = (!is_null($memlevel)) ? '<div class="progress progress-striped active">
<div class="bar bar-'.$memlevel.'" style="width: '.$memory.'%;">'.$memory.'%</div> </div>'
                    : '<div class = "progress progress-striped active">
            <div class = "bar bar-info" style = "width: 100%;">Unknown</div></div>';

            if (0 == $hdd = $data['hdd']) {
                $hddlevel = null;
            } elseif ($hdd >= 51) {
                $hddlevel = "success";
            } elseif ($hdd <= 50) {
                $hddlevel = "warning";
            } elseif ($hdd <= 35) {
                $hddlevel = "danger";
            }
            $data['hdd'] = (!is_null($hddlevel)) ? '<div class="progress progress-striped active">
<div class="bar bar-'.$hddlevel.'" style="width: '.$hdd.'%;">'.$hdd.'%</div></div>'
                    : '<div class = "progress progress-striped active">
            <div class = "bar bar-info" style = "width: 100%;">Unknown</div></div>';

            $data["load"] = number_format($data["load"], 2);
            $data['online'] = '<div class = "progress">
            <div class = "bar bar-success" style = "width: 100%;"><small>Up</small></div>
            </div>';
            echo json_encode($data);
        }
    }